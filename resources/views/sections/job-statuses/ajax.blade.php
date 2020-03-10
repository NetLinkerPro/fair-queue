<content-wrapper v-if="AWES._store.state.fair_queue_job_statuses_ajax &&
AWES._store.state.fair_queue_job_statuses_ajax.data &&
AWES._store.state.fair_queue_job_statuses_ajax.data.length >0 " store-data="fair_queue_job_statuses_ajax">
    <div slot="empty"></div>
    <template slot-scope="ajaxData">
        <h5 class="mb-10">  {{ __('fair-queue::job-statuses.active_jobs') }}</h5>
        <div v-for="job_status in ajaxData.data" >

            <div class="system-notify mb-10 tf-img is-info tf-size-small"
                 :style="'background: linear-gradient(70deg, #4e93e0 '+job_status.progress_percentage+'%, rgb(120,175,239) '+job_status.progress_percentage+'%)'">
                <div class="p-10" >
                    <span>
                    {{ __('fair-queue::job-statuses.job_status') }}: <strong>@{{ job_status.__status }}</strong>
                    </span>
                        <span class="ml-10">
                      {{ __('fair-queue::general.name') }}:  <strong>@{{ job_status.name }}</strong>
                    </span>
                        <span class="ml-10">
                      {{ __('fair-queue::job-statuses.progress_percentage') }}:  <strong>@{{ job_status.progress_percentage }}%</strong>
                    </span>
                    <span class="ml-10">
                      {{ __('fair-queue::general.created_at') }}:  <strong>@{{ job_status.created_at }}</strong>
                    </span>
                    <small v-if="job_status.status === 'executing'" class="ml-10"><i class="icon icon-dots animated infinite flash"></i></small>
                    <a target="_blank" :href="'{{route('fair-queue.job_statuses.index') . '?id='}}' + job_status.id"
                       class="ml-10 tf-size-small cl-caption" style="color:white;"> {{ __('fair-queue::general.details') }}</a>
                </div>
            </div>
        </div>

    </template>

</content-wrapper>

<script type="application/javascript">

    document.addEventListener('DOMContentLoaded', () => {

        AWES.on('fair-queue::job-statuses:load', function () {

            var parameters = @JSON($config['queues']);

            AWES.ajax({job_statuses_ajax: parameters}, '{{route('fair-queue.job_statuses.scope')}}', 'get')
                .then(function (data) {
                    AWES._store.commit('setData', {
                        param: 'fair_queue_job_statuses_ajax',
                        data: data.data
                    });
                });
        });

        AWES.emit('fair-queue::job-statuses:load');

        setInterval(()=>{AWES.emit('fair-queue::job-statuses:load')}, {{config('fair-queue.job_statuses_interval_load')}});
    });
</script>