@extends('fair-queue::vendor.indigo-layout.main')

@section('meta_title', __('fair-queue::horizons.meta_title')  . ' // ' . config('app.name'))
@section('meta_description', __('fair-queue::horizons.meta_description'))

@push('head')
    @include('fair-queue::integration.favicons')
    @include('fair-queue::integration.ga')
@endpush

@section('create_button')
    <button class="frame__header-add" @click="AWES.emit('modal::form:open');"><i class="icon icon-plus"></i></button>
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
                            <span>{{ __('fair-queue::queues.manage') }}</span>
                        </template>
                        <cm-link href="{{route('fair-queue.horizons.index')}}">   {{ __('fair-queue::queues.manage_horizons') }}</cm-link>
                        <cm-link href="{{route('fair-queue.supervisors.index')}}">   {{ __('fair-queue::queues.manage_supervisors') }}</cm-link>
                        <cm-link href="{{route('fair-queue.accesses.index')}}">   {{ __('fair-queue::queues.manage_accesses') }}</cm-link>
                        <cm-link href="{{route('fair-queue.job_statuses.index')}}">   {{ __('fair-queue::queues.job_statuses') }}</cm-link>
                    </context-menu>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        @table([
        'name' => 'queues_table',
        'row_url'=> '',
        'scope_api_url' => route('fair-queue.queues.scope'),
        'scope_api_params' => []
        ])
        <template slot="header">
            <h3>{{__('fair-queue::queues.queue_list') }}</h3>
        </template>
        <tb-column name="no_field_horizon" label="{{ __('fair-queue::queues.horizon') }}" sort>
            <template slot-scope="col">
                 @{{ col.data.horizon.name }}
            </template>
        </tb-column>
        <tb-column name="no_field_supervisor" label="{{ __('fair-queue::queues.supervisor') }}" sort>
            <template slot-scope="col">
                @{{ col.data.supervisor.name }}
            </template>
        </tb-column>
        <tb-column name="name" label="{{ __('fair-queue::general.name') }}" sort>
            <template slot-scope="col">
                @{{ col.data.name }}
            </template>
        </tb-column>
        <tb-column name="queue" label="{{ __('fair-queue::queues.queue') }}" sort>
            <template slot-scope="col">
               <small> @{{ col.data.queue }}</small>
            </template>
        </tb-column>
        <tb-column name="refresh_max_model_id" label="{{ __('fair-queue::queues.refresh_max_model_id') }}" sort>
            <template slot-scope="col">
                @{{ col.data.refresh_max_model_id }}
            </template>
        </tb-column>

        <tb-column name="active" label="{{ __('fair-queue::queues.active') }}" sort>
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
                    <cm-button @click="AWES._store.commit('setData', {param: 'editQueue', data: d.data}); AWES.emit('modal::edit-queue:open')">
                        {{ __('fair-queue::general.edit') }}
                    </cm-button>
                    <cm-button @click="AWES._store.commit('setData', {param: 'deleteQueue', data: d.data}); AWES.emit('modal::delete-queue:open')">
                        {{ __('fair-queue::general.delete') }}
                    </cm-button>
                </context-menu>
            </template>
        </tb-column>
        @endtable

    </div>
@endsection

@section('modals')

    {{--Add queue--}}
    <modal-window name="form" class="modal_formbuilder" title="{{ __('fair-queue::queues.addition_queue') }}">

                <form-builder name="add-queue" url="{{ route('fair-queue.queues.store') }}" @sended="AWES.emit('content::queues_table:update')"
                              send-text="{{ __('fair-queue::general.add') }}"
                              cancel-text="{{ __('fair-queue::general.cancel') }}">
                    <div class="section" v-if="AWES._store.state.forms['add-queue']">

                        <fb-select name="horizon_uuid" label="{{ __('fair-queue::queues.horizon') }}"
                                   url="{{route('fair-queue.horizons.scope')}}" auto-fetch=""
                                   options-value="uuid" options-name="name" :multiple="false" placeholder-text=" "></fb-select>

                        <fb-select name="supervisor_uuid" label="{{ __('fair-queue::queues.supervisor') }}"
                                   url="{{route('fair-queue.supervisors.scope')}}" auto-fetch=""
                                   options-value="uuid" options-name="name" :multiple="false" placeholder-text=" "></fb-select>

                        <fb-input name="name" label="{{ __('fair-queue::general.name') }}"></fb-input>

                        <fb-input name="queue" label="{{ __('fair-queue::queues.queue') }}"></fb-input>

                        <fb-input type="number" name="refresh_max_model_id" label="{{ __('fair-queue::queues.refresh_max_model_id') }}"></fb-input>

                        <fb-switcher name="active" label="{{ __('fair-queue::queues.active') }}"></fb-switcher>

                    </div>
                </form-builder>


    </modal-window>

    {{--Edit queue--}}
    <modal-window name="edit-queue" class="modal_formbuilder" title="{{ __('fair-queue::queues.edition_queue') }}">
        <form-builder name="edit-queue"  method="PATCH" url="{{ route('fair-queue.queues.index') }}/{id}" store-data="editQueue" @sended="AWES.emit('content::queues_table:update')"
                      send-text="{{ __('fair-queue::general.save') }}"
                      cancel-text="{{ __('fair-queue::general.cancel') }}">
            <div class="section" v-if="AWES._store.state.forms['edit-queue']">

                <fb-input name="horizon_uuid" type="hidden"></fb-input>
                <fb-input name="supervisor_uuid" type="hidden"></fb-input>
                <fb-input name="queue" type="hidden"></fb-input>

                <fb-input name="name" label="{{ __('fair-queue::general.name') }}"></fb-input>

                <fb-input type="number" name="refresh_max_model_id" label="{{ __('fair-queue::queues.refresh_max_model_id') }}"></fb-input>

                <fb-switcher name="active" label="{{ __('fair-queue::queues.active') }}"></fb-switcher>

            </div>
        </form-builder>
    </modal-window>

    {{--Delete queue--}}
    <modal-window name="delete-queue" class="modal_formbuilder" title="{{ __('fair-queue::queues.are_you_sure_delete_queue') }}">
        <form-builder name="delete-queue" method="DELETE" url="{{ route('fair-queue.queues.index') }}/{id}" store-data="deleteQueue" @sended="AWES.emit('content::queues_table:update')"
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
