<?php

namespace NetLinker\FairQueue\Sections\JobStatuses\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class JobStatus extends Model implements OwnableContract
{

    const STATUS_QUEUED = 'queued';
    const STATUS_EXECUTING = 'executing';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';
    const STATUS_INTERRUPTED = 'interrupted';

    use SoftDeletes, HasOwner;

    protected $ownerPrimaryKey = 'uuid';
    protected $ownerForeignKey = 'owner_uuid';

    protected $withDefaultOwnerOnCreate = true;

    /**
     * Get owner model name.
     *
     * @return string
     */
    protected function getOwnerModel()
    {
        return config('fair-queue.owner.model');
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fair_queue_job_statuses';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'input' => 'array',
        'output' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'interrupt' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['uuid', 'owner_uuid', 'job_id', 'type', 'external_uuid', 'queue', 'attempts', 'progress_now', 'progress_max', 'interrupt',
        'status', 'input', 'output', 'started_at', 'finished_at', 'logs', 'error', 'name'];

    public $orderable = ['job_id', 'queue', 'type', 'name'];

    protected $encryptable = [];
    /**
     * Resolve entity default owner.
     *
     * @return null|\Cog\Contracts\Ownership\CanBeOwner
     */
    public function resolveDefaultOwner()
    {
        $model =$this->getOwnerModel();
        return $model::where('uuid', static::getAuthUserOwnerUuid())->first();
    }

    /**
     * Get auth user owner Uuid
     *
     * @return mixed
     */
    public static function getAuthUserOwnerUuid(){
        $fieldUuid = config('fair-queue.owner.field_auth_user_owner_uuid');
        return Auth::user()->$fieldUuid;
    }


    /**
     * Binds creating/saving events to create UUIDs (and also prevent them from being overwritten).
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });

        static::saving(function ($model) {
            $original_uuid = $model->getOriginal('uuid');
            if ($original_uuid !== $model->uuid) {
                $model->uuid = $original_uuid;
            }
        });
    }

    /**
     * If the attribute is in the encryptable array
     * then decrypt it.
     *
     * @param  $key
     *
     * @return $value
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (in_array($key, $this->encryptable) && $value !== '') {
            $value = decrypt($value);
        }
        return $value;
    }

    /**
     * If the attribute is in the encryptable array
     * then encrypt it.
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable)) {
            $value = encrypt($value);
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * When need to make sure that we iterate through
     * all the keys.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        foreach ($this->encryptable as $key) {
            if (isset($attributes[$key])) {
                $attributes[$key] = decrypt($attributes[$key]);
            }
        }
        return $attributes;
    }

    /**
     * Get progress percentage
     *
     * @return float|int
     */
    public function getProgressPercentageAttribute()
    {
        return $this->progress_max != 0 ? round(100 * $this->progress_now / $this->progress_max) : 0;
    }

    /**
     * Get is ended
     *
     * @return bool
     */
    public function getIsEndedAttribute()
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_FINISHED]);
    }

    /**
     * Get is finished
     *
     * @return bool
     */
    public function getIsFinishedAttribute()
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * Get is interrupted
     *
     * @return bool
     */
    public function getIsInterruptedAttribute()
    {
        return $this->status === self::STATUS_INTERRUPTED;
    }

    /**
     * Get is failed
     *
     * @return bool
     */
    public function getIsFailedAttribute()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Get is executing
     *
     * @return bool
     */
    public function getIsExecutingAttribute()
    {
        return $this->status === self::STATUS_EXECUTING;
    }

    /**
     * Get is queued
     *
     * @return bool
     */
    public function getIsQueuedAttribute()
    {
        return $this->status === self::STATUS_QUEUED;
    }

    /**
     * Get allowed statuses
     *
     * @return array
     */
    public static function getAllowedStatuses()
    {
        return [
            self::STATUS_QUEUED,
            self::STATUS_EXECUTING,
            self::STATUS_FINISHED,
            self::STATUS_FAILED,
            self::STATUS_INTERRUPTED,
        ];
    }
}

