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
                            <span>{{ __('fair-queue::supervisors.manage') }}</span>
                        </template>
                        <cm-link href="{{route('fair-queue.horizons.index')}}">   {{ __('fair-queue::supervisors.manage_horizons') }}</cm-link>
                        <cm-link href="{{route('fair-queue.queues.index')}}">   {{ __('fair-queue::supervisors.manage_queues') }}</cm-link>
                        <cm-link href="{{route('fair-queue.accesses.index')}}">   {{ __('fair-queue::supervisors.manage_accesses') }}</cm-link>
                        <cm-link href="{{route('fair-queue.job_statuses.index')}}">   {{ __('fair-queue::supervisors.job_statuses') }}</cm-link>
                    </context-menu>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        @table([
        'name' => 'supervisors_table',
        'row_url'=> '',
        'scope_api_url' => route('fair-queue.supervisors.scope'),
        'scope_api_params' => []
        ])
        <template slot="header">
            <h3>{{__('fair-queue::supervisors.supervisor_list') }}</h3>
        </template>

        <tb-column name="name" label="{{ __('fair-queue::general.name') }}" sort>
            <template slot-scope="col">
                @{{ col.data.name }}
            </template>
        </tb-column>
        <tb-column name="environment" label="{{ __('fair-queue::supervisors.environment') }}" sort>
            <template slot-scope="col">
                @{{ col.data.environment }}
            </template>
        </tb-column>
        <tb-column name="connection" label="{{ __('fair-queue::supervisors.connection') }}" sort>
            <template slot-scope="col">
                @{{ col.data.connection }}
            </template>
        </tb-column>
        <tb-column name="balance" label="{{ __('fair-queue::supervisors.balance') }}" sort>
            <template slot-scope="col">
                @{{ col.data.balance }}
            </template>
        </tb-column>

        <tb-column name="min_processes" label="{{ __('fair-queue::supervisors.min_processes') }}" sort>
            <template slot-scope="col">
                @{{ col.data.min_processes }}
            </template>
        </tb-column>
        <tb-column name="max_processes" label="{{ __('fair-queue::supervisors.max_processes') }}" sort>
            <template slot-scope="col">
                @{{ col.data.max_processes }}
            </template>
        </tb-column>
        <tb-column name="priority" label="{{ __('fair-queue::supervisors.priority') }}" sort>
            <template slot-scope="col">
                @{{ col.data.priority }}
            </template>
        </tb-column>
        
        <tb-column name="active" label="{{ __('fair-queue::supervisors.active') }}" sort>
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
                    <cm-button @click="AWES._store.commit('setData', {param: 'editSupervisor', data: d.data}); AWES.emit('modal::edit-queue:open')">
                        {{ __('fair-queue::general.edit') }}
                    </cm-button>
                    <cm-button @click="AWES._store.commit('setData', {param: 'deleteSupervisor', data: d.data}); AWES.emit('modal::delete-queue:open')">
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
    <modal-window name="form" class="modal_formbuilder" title="{{ __('fair-queue::supervisors.addition_supervisor') }}">
        <form-builder url="{{ route('fair-queue.supervisors.store') }}" @sended="AWES.emit('content::supervisors_table:update')"
                      send-text="{{ __('fair-queue::general.add') }}"
                      cancel-text="{{ __('fair-queue::general.cancel') }}">
            <div class="section">
                <fb-input name="name" label="{{ __('fair-queue::general.name') }}"></fb-input>
                <fb-input name="environment" label="{{ __('fair-queue::supervisors.environment') }}"></fb-input>
                <fb-input name="connection" label="{{ __('fair-queue::supervisors.connection') }}"></fb-input>
                <fb-input name="balance" label="{{ __('fair-queue::supervisors.balance') }}"></fb-input>
                <fb-input type="number" name="min_processes" label="{{ __('fair-queue::supervisors.min_processes') }}"></fb-input>
                <fb-input type="number" name="max_processes" label="{{ __('fair-queue::supervisors.max_processes') }}"></fb-input>
                <fb-input type="number" name="priority" label="{{ __('fair-queue::supervisors.priority') }}"></fb-input>
                <fb-switcher name="active" label="{{ __('fair-queue::supervisors.active') }}"></fb-switcher>
            </div>
        </form-builder>
    </modal-window>

    {{--Edit queue--}}
    <modal-window name="edit-queue" class="modal_formbuilder" title="{{ __('fair-queue::supervisors.edition_supervisor') }}">
        <form-builder method="PATCH" url="{{ route('fair-queue.supervisors.index') }}/{id}" store-data="editSupervisor" @sended="AWES.emit('content::supervisors_table:update')"
                      send-text="{{ __('fair-queue::general.save') }}"
                      cancel-text="{{ __('fair-queue::general.cancel') }}">
            <fb-input name="name" label="{{ __('fair-queue::general.name') }}"></fb-input>
            <fb-input name="environment" label="{{ __('fair-queue::supervisors.environment') }}"></fb-input>
            <fb-input name="connection" label="{{ __('fair-queue::supervisors.connection') }}"></fb-input>
            <fb-input name="balance" label="{{ __('fair-queue::supervisors.balance') }}"></fb-input>
            <fb-input type="number" name="min_processes" label="{{ __('fair-queue::supervisors.min_processes') }}"></fb-input>
            <fb-input type="number" name="max_processes" label="{{ __('fair-queue::supervisors.max_processes') }}"></fb-input>
            <fb-input type="number" name="priority" label="{{ __('fair-queue::supervisors.priority') }}"></fb-input>
            <fb-switcher name="active" label="{{ __('fair-queue::supervisors.active') }}"></fb-switcher>
        </form-builder>
    </modal-window>

    {{--Delete queue--}}
    <modal-window name="delete-queue" class="modal_formbuilder" title="{{ __('fair-queue::supervisors.are_you_sure_delete_supervisor') }}">
        <form-builder name="delete-queue" method="DELETE" url="{{ route('fair-queue.supervisors.index') }}/{id}" store-data="deleteSupervisor" @sended="AWES.emit('content::supervisors_table:update')"
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
