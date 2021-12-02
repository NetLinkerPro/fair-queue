@extends('fair-queue::vendor.indigo-layout.main')

@section('meta_title', __('fair-queue::job-statuses.meta_title')  . ' // ' . config('app.name'))
@section('meta_description', __('fair-queue::job-statuses.meta_description'))

@push('head')
    @include('fair-queue::integration.favicons')
    @include('fair-queue::integration.ga')
@endpush

@section('create_button')

@endsection

@section('content')
    <div class="filter">
        <div class="grid grid-align-center grid-justify-between grid-justify-center--mlg">
            <div class="cell-inline cell-1-1--mlg">
                <div class="grid grid-ungap">
                    <div class="cell-inline cell-1-1--mlg">
                        @filtergroup(['filter' => ['' => __('fair-queue::general.all'), 'queued' =>
                        __('fair-queue::job-statuses.queued'), 'executing' => __('fair-queue::job-statuses.executing')
                        , 'finished' => __('fair-queue::job-statuses.finished'), 'failed' =>
                        __('fair-queue::job-statuses.failed'),
                        'interrupted' => __('fair-queue::job-statuses.interrupted'),
                        'canceled' => __('fair-queue::job-statuses.canceled')
                        ], 'variable' => 'status', 'default' => ''])
                    </div>
                </div>
            </div>
            <div class="cell-inline">
                <div class="filter__rlink">
                    <context-menu button-class="filter__slink" right>
                        <template slot="toggler">
                            <span>{{ __('fair-queue::general.manage') }}</span>
                        </template>
                        <cm-link href="{{route('fair-queue.supervisors.index')}}">   {{ __('fair-queue::general.manage_horizons') }}</cm-link>
                        <cm-link href="{{route('fair-queue.supervisors.index')}}">   {{ __('fair-queue::general.manage_supervisors') }}</cm-link>
                        <cm-link href="{{route('fair-queue.queues.index')}}">   {{ __('fair-queue::general.manage_queues') }}</cm-link>
                        <cm-link href="{{route('fair-queue.accesses.index')}}">   {{ __('fair-queue::general.manage_accesses') }}</cm-link>

                    </context-menu>
                </div>
                <div class="filter__rlink">
                    <context-menu button-class="filter__slink" right>
                        <template slot="toggler">
                            <span>{{  __('fair-queue::general.sort_by') }}</span>
                        </template>
                        <cm-query :param="{orderBy: 'name'}">{{  __('fair-queue::general.name') }} &uarr;
                        </cm-query>
                        <cm-query :param="{orderBy: 'name_desc'}">
                            {{  __('fair-queue::general.name') }} &darr;
                        </cm-query>
                        <cm-query :param="{orderBy: 'job_id'}">{{  __('fair-queue::job-statuses.job_id') }} &uarr;
                        </cm-query>
                        <cm-query :param="{orderBy: 'job_id_desc'}">
                            {{  __('fair-queue::job-statuses.job_id') }} &darr;
                        </cm-query>
                        <cm-query :param="{orderBy: 'queue'}">{{  __('fair-queue::job-statuses.queue') }} &uarr;
                        </cm-query>
                        <cm-query :param="{orderBy: 'queue_desc'}">{{  __('fair-queue::job-statuses.queue') }} &darr;
                        </cm-query>
                        <cm-query :param="{orderBy: 'type'}">{{  __('fair-queue::general.type') }} &uarr;</cm-query>
                        <cm-query :param="{orderBy: 'type_desc'}">{{  __('fair-queue::general.type') }} &darr;
                        </cm-query>
                    </context-menu>
                </div>
                <div class="filter__rlink">
                    <button class="filter__slink" @click="$refs.filter.toggle()">
                        <i class="icon icon-filter" v-if="">
                            <span class="icn-dot" v-if="$awesFilters.state.active['job_statuses']"></span>
                        </i>
                        {{  __('fair-queue::general.filter') }}
                    </button>
                </div>
            </div>
            <slide-up-down ref="filter">
                <filter-wrapper name="job_statuses" send-text="{{ __('fair-queue::general.apply') }}"
                                reset-text="{{ __('fair-queue::general.reset') }}">
                    <div class="grid grid-gap-x grid_forms">
                        <div class="cell">
                            <fb-input name="id" label="{{ __('fair-queue::general.id') }}"></fb-input>
                            <fb-input name="name" label="{{ __('fair-queue::general.name') }}"></fb-input>
                            <fb-input name="job_id" label="{{ __('fair-queue::job-statuses.job_id') }}"></fb-input>
                            <fb-input name="status" label="{{ __('fair-queue::general.status') }}"></fb-input>
                            <fb-input name="external_uuid"
                                      label="{{ __('fair-queue::job-statuses.external_uuid') }}"></fb-input>
                            <fb-input name="type" label="{{ __('fair-queue::general.type') }}"></fb-input>
                        </div>
                    </div>
                </filter-wrapper>
            </slide-up-down>
        </div>
    </div>

    <div class="section">
        @table([
            'name' => 'job_statuses_table',
            'row_url'=> '',
            'scope_api_url' => route('fair-queue.job_statuses.scope'),
            'scope_api_params' => ['status', 'external_uuid', 'orderBy', 'type', 'queue', 'job_id', 'name', 'job_id', 'id']
        ])
        <template slot="header">
            <h3>{{__('fair-queue::job-statuses.job_list') }}</h3>
        </template>
        <tb-column name="name" label="{{ __('fair-queue::general.name') }}" sort>
            <template slot-scope="col">
                @{{ col.data.name }}
            </template>
        </tb-column>
        <tb-column :width="80" name="job_id" label="{{ __('fair-queue::job-statuses.job_id') }}" sort>
            <template slot-scope="col">
                <div style="max-width: 85px; overflow-wrap: break-word;">
                    <small class="cl-caption">@{{ col.data.job_id }}</small>
                </div>
            </template>
        </tb-column>
        <tb-column name="external_uuid" label="{{ __('fair-queue::job-statuses.external_uuid') }}">
            <template slot-scope="col">
                <div style="max-width: 115px; overflow-wrap: break-word;">
                    <small class="cl-caption">@{{ col.data.external_uuid }}</small>
                </div>
            </template>
        </tb-column>
        <tb-column name="type" label="{{ __('fair-queue::general.type') }}" sort>
            <template slot-scope="col">
                <div style="max-width: 80px; overflow-wrap: break-word;">
                    <small class="cl-caption">@{{ col.data.type }}</small>
                </div>
            </template>
        </tb-column>
        <tb-column name="queue" label="{{ __('fair-queue::job-statuses.queue') }}" sort>
            <template slot-scope="col">
                <div style="max-width: 80px; overflow-wrap: break-word;">
                    <small class="cl-caption">@{{ col.data.queue }}</small>
                </div>
            </template>
        </tb-column>
        <tb-column name="progress_percentage" label="{{ __('fair-queue::job-statuses.progress_percentage') }}">
            <template slot-scope="col">
                @{{ col.data.progress_percentage }} %
            </template>
        </tb-column>
        <tb-column name="__status" label="{{ __('fair-queue::general.status') }}">
            <template slot-scope="col">
                @{{ col.data.__status }}
            </template>
        </tb-column>
        <tb-column name="no_field_data" label="{{ __('fair-queue::general.data') }}" sort>
            <template slot-scope="col">
                <div v-if="col.data.input">
                   <small>
                       <a class="cl-caption" @click="AWES._store.commit('setData', {param: 'displayInputJobStatus', data: col.data}); AWES.emit('modal::display_input_job_status:open')">
                           {{ __('fair-queue::job-statuses.data_input_short') }}
                       </a>
                   </small>
                </div>
                <div v-if="col.data.output">
                    <small>
                        <a class="cl-caption" @click="AWES._store.commit('setData', {param: 'displayOutputJobStatus', data: col.data}); AWES.emit('modal::display_output_job_status:open')">
                            {{ __('fair-queue::job-statuses.data_output_short') }}
                        </a>
                    </small>
                </div>
                <div v-if="col.data.logs">
                    <small>
                        <a @click="AWES._store.commit('setData', {param: 'displayLogsJobStatus', data: col.data}); AWES.emit('modal::display_logs_job_status:open')">
                            {{ __('fair-queue::general.logs') }}
                        </a>
                    </small>
                </div>
                <div v-if="col.data.error">
                   <small>
                       <a class="cl-red" @click="AWES._store.commit('setData', {param: 'displayErrorJobStatus', data: col.data}); AWES.emit('modal::display_error_job_status:open')">
                           {{ __('fair-queue::general.error') }}
                       </a>
                   </small>
                </div>
            </template>
        </tb-column>
        <tb-column name="started_at" label="{{ __('fair-queue::job-statuses.horizon') }}">
            <template slot-scope="col">
                <div v-if="col.data.horizon">
                    <small class="cl-caption">@{{ col.data.horizon.name }}</small>
                </div>
            </template>
        </tb-column>
        <tb-column name="started_at" label="{{ __('fair-queue::general.started_at') }}">
            <template slot-scope="col">
                <small class="cl-caption">@{{ col.data.started_at }}</small>
            </template>
        </tb-column>
        <tb-column name="finished_at" label="{{ __('fair-queue::general.finished_at') }}">
            <template slot-scope="col">
                <small class="cl-caption">@{{ col.data.finished_at }}</small>
            </template>
        </tb-column>
                    <tb-column name="no_field_options" label="{{ __('fair-queue::general.options') }}">
                        <template slot-scope="col">
                            <context-menu right boundary="table">
                                <button type="submit" slot="toggler" class="btn">
                                    {{ __('fair-queue::general.options') }}
                                </button>
                                <cm-button v-if="col.data.status === 'executing'"
                                        @click="AWES._store.commit('setData', {param: 'interruptJobStatus', data: col.data}); AWES.emit('modal::interrupt_job_status:open')">
                                    {{ __('fair-queue::general.interrupt') }}
                                </cm-button>
                                <cm-button v-if="col.data.status === 'queued'"
                                           @click="AWES._store.commit('setData', {param: 'cancelJobStatus', data: col.data}); AWES.emit('modal::cancel_job_status:open')">
                                    {{ __('fair-queue::general.cancel') }}
                                </cm-button>
                            </context-menu>
                        </template>
                    </tb-column>
        @endtable
    </div>
@endsection

@section('modals')

    {{--Display error--}}
    <modal-window name="display_error_job_status" class="modal_formbuilder" title="{{ __('fair-queue::general.error') }}" theme="fullscreen">
        <form-builder method="GET" url="" store-data="displayErrorJobStatus" auto-submit>
            <fb-textarea rows="20" name="error" label="{{ __('fair-queue::general.error') }}" readonly></fb-textarea>
        </form-builder>
    </modal-window>

    {{--Display logs--}}
    <modal-window name="display_logs_job_status" class="modal_formbuilder" title="{{ __('fair-queue::general.logs') }}" theme="fullscreen">
        <form-builder method="GET" url="" store-data="displayLogsJobStatus" auto-submit>
            <fb-textarea rows="20" name="logs" label="{{ __('fair-queue::general.logs') }}" readonly></fb-textarea>
        </form-builder>
    </modal-window>

    {{--Display input--}}
    <modal-window name="display_input_job_status" class="modal_formbuilder" title="{{ __('fair-queue::job-statuses.data_input') }}">
        <form-builder method="GET" url="" store-data="displayInputJobStatus" auto-submit>
            <fb-textarea rows="20" name="input" label="{{ __('fair-queue::job-statuses.data_input') }}" readonly></fb-textarea>
        </form-builder>
    </modal-window>

    {{--Display output--}}
    <modal-window name="display_output_job_status" class="modal_formbuilder" title="{{ __('fair-queue::job-statuses.data_output') }}">
        <form-builder method="GET" url="" store-data="displayOutputJobStatus" auto-submit>
            <fb-textarea rows="20" name="output" label="{{ __('fair-queue::job-statuses.data_input') }}" readonly></fb-textarea>
        </form-builder>
    </modal-window>

    {{--Interrupt job status--}}
    <modal-window name="interrupt_job_status" class="modal_formbuilder" title="{{ __('fair-queue::job-statuses.are_you_sure_interrupt_job_status') }}">
        <form-builder name="interrupt_job_status" method="POST" url="{{ route('fair-queue.job_statuses.interrupt') }}" store-data="interruptJobStatus" @sended="AWES.emit('content::job_statuses_table:update')"
                      send-text="{{ __('fair-queue::general.yes') }}"
                      cancel-text="{{ __('fair-queue::general.no') }}"
                      disabled-dialog>
            <template slot-scope="fields">

                <fb-input name="id" type="hidden"></fb-input>
                <input type="hidden" name="id" :value="fields.id"/>
                <!-- Fix enable button yes for delete -->
                <input type="hidden" name="isEdited" :value="AWES._store.state.forms['interrupt_job_status']['isEdited'] = true"/>
            </template>
        </form-builder>
    </modal-window>

    {{--Cancel job status--}}
    <modal-window name="cancel_job_status" class="modal_formbuilder" title="{{ __('fair-queue::job-statuses.are_you_sure_cancel_job_status') }}">
        <form-builder name="cancel_job_status" method="POST" url="{{ route('fair-queue.job_statuses.cancel') }}" store-data="cancelJobStatus" @sended="AWES.emit('content::job_statuses_table:update')"
                      send-text="{{ __('fair-queue::general.yes') }}"
                      cancel-text="{{ __('fair-queue::general.no') }}"
                      disabled-dialog>
            <template slot-scope="fields">

                <fb-input name="id" type="hidden"></fb-input>
                <input type="hidden" name="id" :value="fields.id"/>
                <!-- Fix enable button yes for delete -->
                <input type="hidden" name="isEdited" :value="AWES._store.state.forms['cancel_job_status']['isEdited'] = true"/>
            </template>
        </form-builder>
    </modal-window>

@endsection
