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
                            <span>{{ __('fair-queue::horizons.manage') }}</span>
                        </template>
                        <cm-link href="{{route('fair-queue.supervisors.index')}}">   {{ __('fair-queue::horizons.manage_supervisors') }}</cm-link>
                        <cm-link href="{{route('fair-queue.queues.index')}}">   {{ __('fair-queue::horizons.manage_queues') }}</cm-link>
                        <cm-link href="{{route('fair-queue.accesses.index')}}">   {{ __('fair-queue::horizons.manage_accesses') }}</cm-link>
                        <cm-link href="{{route('fair-queue.job_statuses.index')}}">   {{ __('fair-queue::horizons.job_statuses') }}</cm-link>

                    </context-menu>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        @table([
        'name' => 'horizons_table',
        'row_url'=> '',
        'scope_api_url' => route('fair-queue.horizons.scope'),
        'scope_api_params' => []
        ])
        <template slot="header">
            <h3>{{__('fair-queue::horizons.horizon_list') }}</h3>
        </template>
        <tb-column name="name" label="{{ __('fair-queue::general.name') }}" sort>
            <template slot-scope="col">
                @{{ col.data.name }}
            </template>
        </tb-column>
        <tb-column name="uuid" label="{{ __('fair-queue::general.uuid') }}" sort>
            <template slot-scope="col">
                @{{ col.data.uuid }}
            </template>
        </tb-column>
        <tb-column name="active" label="{{ __('fair-queue::horizons.active') }}" sort>
            <template slot-scope="col">
                <span v-if="col.data.active">{{ __('fair-queue::general.yes') }}</span>
                <span v-else>{{ __('fair-queue::general.no') }}</span>
            </template>
        </tb-column>
        <tb-column name="ip" label="{{ __('fair-queue::general.ip') }}" sort>
            <template slot-scope="col">
                @{{ col.data.ip }}
            </template>
        </tb-column>
        <tb-column name="no_field_trim" label="{{ __('fair-queue::horizons.trimming') }}" sort>
            <template slot-scope="col">
               <div><small class="cl-caption"> {{ __('fair-queue::horizons.recent') }}: @{{ col.data.trim_recent }}</small></div>
                <div><small class="cl-caption"> {{ __('fair-queue::horizons.recent_failed') }}: @{{ col.data.trim_recent_failed }}</small></div>
                <div><small class="cl-caption"> {{ __('fair-queue::horizons.failed') }}: @{{ col.data.trim_failed }}</small></div>
                <div><small class="cl-caption"> {{ __('fair-queue::horizons.monitored') }}: @{{ col.data.trim_monitored }}</small></div>
            </template>
        </tb-column>

        <tb-column name="memory_limit" label="{{ __('fair-queue::horizons.memory_limit') }}" sort>
            <template slot-scope="col">
                @{{ col.data.memory_limit }}
            </template>
        </tb-column>
        <tb-column name="no_field_options" label="{{ __('fair-queue::general.options') }}">
            <template slot-scope="d">

                <context-menu right boundary="table">
                    <button type="submit" slot="toggler" class="btn">
                        {{ __('fair-queue::general.options') }}
                    </button>
                    <cm-button @click="AWES._store.commit('setData', {param: 'editHorizon', data: d.data}); AWES.emit('modal::edit-horizon:open')">
                        {{ __('fair-queue::general.edit') }}
                    </cm-button>
                    <cm-button @click="AWES._store.commit('setData', {param: 'deleteHorizon', data: d.data}); AWES.emit('modal::delete-horizon:open')">
                        {{ __('fair-queue::general.delete') }}
                    </cm-button>
                </context-menu>
            </template>
        </tb-column>
        @endtable
    </div>
@endsection

@section('modals')

    {{--Add horizon--}}
    <modal-window name="form" class="modal_formbuilder" title="{{ __('fair-queue::horizons.addition_horizon') }}">
        <form-builder url="{{ route('fair-queue.horizons.store') }}" @sended="AWES.emit('content::horizons_table:update')"
                      send-text="{{ __('fair-queue::general.add') }}"
                      cancel-text="{{ __('fair-queue::general.cancel') }}">
            <div class="section">
                <fb-input name="name" label="{{ __('fair-queue::general.name') }}"></fb-input>
                <fb-input type="number" name="memory_limit" label="{{ __('fair-queue::horizons.memory_limit') }}"></fb-input>
                <fb-input type="number" name="trim_recent" label="{{ __('fair-queue::horizons.recent') }}"></fb-input>
                <fb-input type="number" name="trim_recent_failed" label="{{ __('fair-queue::horizons.recent_failed') }}"></fb-input>
                <fb-input type="number" name="trim_failed" label="{{ __('fair-queue::horizons.failed') }}"></fb-input>
                <fb-input type="number" name="trim_monitored" label="{{ __('fair-queue::horizons.monitored') }}"></fb-input>
                <fb-switcher name="active" label="{{ __('fair-queue::horizons.active') }}"></fb-switcher>
            </div>
        </form-builder>
    </modal-window>

    {{--Edit horizon--}}
    <modal-window name="edit-horizon" class="modal_formbuilder" title="{{ __('fair-queue::horizons.edition_horizon') }}">
        <form-builder method="PATCH" url="{{ route('fair-queue.horizons.index') }}/{id}" store-data="editHorizon" @sended="AWES.emit('content::horizons_table:update')"
                      send-text="{{ __('fair-queue::general.save') }}"
                      cancel-text="{{ __('fair-queue::general.cancel') }}">
            <fb-input name="name" label="{{ __('fair-queue::general.name') }}"></fb-input>
            <fb-input type="number" name="memory_limit" label="{{ __('fair-queue::horizons.memory_limit') }}"></fb-input>
            <fb-input type="number" name="trim_recent" label="{{ __('fair-queue::horizons.recent') }}"></fb-input>
            <fb-input type="number" name="trim_recent_failed" label="{{ __('fair-queue::horizons.recent_failed') }}"></fb-input>
            <fb-input type="number" name="trim_failed" label="{{ __('fair-queue::horizons.failed') }}"></fb-input>
            <fb-input type="number" name="trim_monitored" label="{{ __('fair-queue::horizons.monitored') }}"></fb-input>
            <fb-switcher name="active" label="{{ __('fair-queue::horizons.active') }}"></fb-switcher>
        </form-builder>
    </modal-window>

    {{--Delete horizon--}}
    <modal-window name="delete-horizon" class="modal_formbuilder" title="{{ __('fair-queue::horizons.are_you_sure_delete_horizon') }}">
        <form-builder name="delete-horizon" method="DELETE" url="{{ route('fair-queue.horizons.index') }}/{id}" store-data="deleteHorizon" @sended="AWES.emit('content::horizons_table:update')"
                      send-text="{{ __('fair-queue::general.yes') }}"
                      cancel-text="{{ __('fair-queue::general.no') }}"
                      disabled-dialog>
            <template slot-scope="block">

                <!-- Fix enable button yes for delete -->
                <input type="hidden" name="isEdited" :value="AWES._store.state.forms['delete-horizon']['isEdited'] = true"/>
            </template>
        </form-builder>
    </modal-window>

@endsection
