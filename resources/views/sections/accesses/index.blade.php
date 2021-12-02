@extends('fair-queue::vendor.indigo-layout.main')

@section('meta_title', __('fair-queue::horizons.meta_title')  . ' // ' . config('app.name'))
@section('meta_description', __('fair-queue::horizons.meta_description'))

@push('head')
    @include('fair-queue::integration.favicons')
    @include('fair-queue::integration.ga')
@endpush

@section('create_button')
    <button class="frame__header-add" @click="AWES.emit('modal::form:open')"><i class="icon icon-plus"></i></button>
@endsection

@section('content')
    <div class="filter">
        <div class="grid grid-align-center grid-justify-between grid-justify-center--mlg">
            <div class="cell-inline cell-1-1--mlg">

            </div>
            <div class="cell-inline">
                <div class="filter__rlink">
                    <context-menu button-class="filter__slink" right>
                        <template slot="toggler">
                            <span>{{ __('fair-queue::general.manage') }}</span>
                        </template>
                        <cm-link href="{{route('fair-queue.horizons.index')}}">   {{ __('fair-queue::general.manage_horizons') }}</cm-link>
                        <cm-link href="{{route('fair-queue.supervisors.index')}}">   {{ __('fair-queue::general.manage_supervisors') }}</cm-link>
                        <cm-link href="{{route('fair-queue.queues.index')}}">   {{ __('fair-queue::general.manage_queues') }}</cm-link>
                        <cm-link href="{{route('fair-queue.job_statuses.index')}}">   {{ __('fair-queue::general.manage_job_statuses') }}</cm-link>
                    </context-menu>
                </div>
            </div>
        </div>
    </div>

    @if(\Illuminate\Support\Facades\App::runningUnitTests())
        @jobstatuses([
            'queues' => ['fair_queue_test_job_status']
        ])

        <form-builder name="test" method="POST" url="{{ route('fair-queue.accesses.test') }}"
                      @sended="AWES.emit('fair-queue::job-statuses:load')">
            <template slot-scope="block">

                <!-- Fix enable button yes for delete -->
                <input type="hidden" name="isEdited" :value="AWES._store.state.forms['test']['isEdited'] = true"/>
            </template>
        </form-builder>
    @endif
    <div class="section">
        @table([
            'name' => 'accesses_table',
            'row_url'=> '',
            'scope_api_url' => route('fair-queue.accesses.scope'),
            'scope_api_params' => []
        ])
        <template slot="header">
            <h3>{{__('fair-queue::accesses.access_list') }}</h3>
        </template>
        <tb-column name="no_field_queue" label="{{ __('fair-queue::accesses.queue') }}" sort>
            <template slot-scope="col">
                <div class="mb-5">@{{ col.data.queue.queue }}</div>

                <small class="cl-caption">@{{ col.data.queue_uuid }}</small>

            </template>
        </tb-column>
        <tb-column name="name" label="{{ __('fair-queue::general.name') }}" sort>
            <template slot-scope="col">
                @{{ col.data.name }}
            </template>
        </tb-column>

        <tb-column name="description" label="{{ __('fair-queue::general.description') }}" sort>
            <template slot-scope="col">
                @{{ col.data.description }}
            </template>
        </tb-column>
        <tb-column name="type" label="{{ __('fair-queue::general.type') }}" sort>
            <template slot-scope="col">
                @{{ col.data.type }}
            </template>
        </tb-column>
        <tb-column name="object_uuid" label="{{ __('fair-queue::general.object') }}" sort>
            <template slot-scope="col">

                <small class="cl-caption">@{{ col.data.object_uuid }}</small>
            </template>
        </tb-column>
        <tb-column name="active" label="{{ __('fair-queue::accesses.active') }}" sort>
            <template slot-scope="col">
                <span v-if="col.data.active">{{ __('fair-queue::general.yes') }}</span>
                <span v-else>{{ __('fair-queue::general.no') }}</span>
            </template>
        </tb-column>

        <tb-column name="no_field_options" label="{{ __('fair-queue::general.options') }}">
            <template slot-scope="d">

                <context-menu right boundary="table">
                    <button type="submit" slot="toggler" class="btn">
                        {{ __('fair-queue::general.options') }}
                    </button>
                    <cm-button @click="AWES._store.commit('setData', {param: 'editAccess', data: d.data}); AWES.emit('modal::edit-access:open')">
                        {{ __('fair-queue::general.edit') }}
                    </cm-button>
                    <cm-button @click="AWES._store.commit('setData', {param: 'deleteAccess', data: d.data}); AWES.emit('modal::delete-access:open')">
                        {{ __('fair-queue::general.delete') }}
                    </cm-button>
                </context-menu>
            </template>
        </tb-column>
        @endtable
    </div>
@endsection

@section('modals')

    {{--Add access--}}
    <modal-window name="form" class="modal_formbuilder" title="{{ __('fair-queue::accesses.addition_access') }}">
        <form-builder name="add-access" url="{{ route('fair-queue.accesses.store') }}" @sended="AWES.emit('content::accesses_table:update')"
                      send-text="{{ __('fair-queue::general.add') }}"
                      cancel-text="{{ __('fair-queue::general.cancel') }}">
            <div class="section" v-if="AWES._store.state.forms['add-access']">

                <fb-select name="queue_uuid" label="{{ __('fair-queue::accesses.queue') }}"
                           :disabled="!!AWES._store.state.forms['add-access'].fields.queue_uuid"
                           url="{{route('fair-queue.queues.scope')}}" auto-fetch=""
                           options-value="uuid" options-name="name" :multiple="false" placeholder-text=" "></fb-select>

                <fb-input name="name" label="{{ __('fair-queue::general.name') }}"></fb-input>

                <fb-textarea name="description" label="{{ __('fair-queue::general.description') }}"></fb-textarea>

                <fb-select name="type" label="{{ __('fair-queue::general.type') }}"
                           :select-options="[{name: '{{ __('fair-queue::accesses.allow') }}', value:'allow'}, {name: '{{ __('fair-queue::accesses.exclude') }}', value:'exclude'}]"
                           options-value="value" options-name="name" :multiple="false" placeholder-text=" "></fb-select>

                <fb-select v-if="AWES._store.state.forms['add-access'].fields.queue_uuid"
                        name="object_uuid" label="{{ __('fair-queue::general.object') }}"
                           :url="'{{route('fair-queue.accesses.objects')}}?queue_uuid=' + AWES._store.state.forms['add-access'].fields.queue_uuid + '&q=%s'" auto-fetch=""
                           options-value="uuid" options-name="name" :multiple="false" placeholder-text=" "></fb-select>

                <fb-switcher name="active" label="{{ __('fair-queue::accesses.active') }}"></fb-switcher>

            </div>
        </form-builder>
    </modal-window>

    {{--Edit access--}}
    <modal-window name="edit-access" class="modal_formbuilder" title="{{ __('fair-queue::accesses.edition_access') }}">
        <form-builder method="PATCH" url="{{ route('fair-queue.accesses.index') }}/{id}" store-data="editAccess" @sended="AWES.emit('content::accesses_table:update')"
                      send-text="{{ __('fair-queue::general.save') }}"
                      cancel-text="{{ __('fair-queue::general.cancel') }}">

            <fb-input type="hidden" name="queue_uuid"></fb-input>
            <fb-input type="hidden" name="queue_uuid"></fb-input>
            <fb-input type="hidden" name="object_uuid"></fb-input>
            <fb-input type="hidden" name="type"></fb-input>

            <fb-input name="name" label="{{ __('fair-queue::general.name') }}"></fb-input>
            <fb-textarea name="description" label="{{ __('fair-queue::general.description') }}"></fb-textarea>

            <fb-switcher name="active" label="{{ __('fair-queue::accesses.active') }}"></fb-switcher>

        </form-builder>
    </modal-window>

    {{--Delete queue--}}
    <modal-window name="delete-access" class="modal_formbuilder" title="{{ __('fair-queue::accesses.are_you_sure_delete_accesse') }}">
        <form-builder name="delete-access" method="DELETE" url="{{ route('fair-queue.accesses.index') }}/{id}" store-data="deleteAccess" @sended="AWES.emit('content::accesses_table:update')"
                      send-text="{{ __('fair-queue::general.yes') }}"
                      cancel-text="{{ __('fair-queue::general.no') }}"
                      disabled-dialog>
            <template slot-scope="block">

                <!-- Fix enable button yes for delete -->
                <input type="hidden" name="isEdited" :value="AWES._store.state.forms['delete-queue']['isEdited'] = true"/>
            </template>
        </form-builder>
    </modal-window>

@endsection
