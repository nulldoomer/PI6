@forelse($pastapp as $apps)
    <tr>
        <td>
            <div class="user-info">
                <img class="user-image"
                    src="{{ isset($apps->doctor->user->image) ? asset(path_user_image() . $apps->doctor->user->image) : Avatar::create($apps->doctor->user->name)->toBase64() }}"
                    alt="{{ __('doctor-image') }}" />
                <h3 class="user-name">{{ $apps->doctor->user->name }}</h3>
            </div>
        </td>
        <td>{{ $apps->appdate }}</td>
        <td>{{ $apps->slot ? Carbon\Carbon::parse($apps->slot->start_time)->format('H:i A') : 'N/A' }}</td>
        <td>{{ $apps->type == OFFLINE ? __('main.Offline') : __('main.Online') }}</td>
        <td>
            @if (Carbon\Carbon::parse($apps->appdate) < Carbon\Carbon::today() && $apps->status == 0)
                <span class="text-danger">
                    Cancelada
                </span>
            @endif
            @if (Carbon\Carbon::parse($apps->appdate) < Carbon\Carbon::today() && $apps->status == 1)
                <span class="text-danger">
                    Cancelada
                </span>
            @endif
            @if ($apps->status == 2)
                <span class="completed">
                    Completada
                </span>
            @endif
            @if ($apps->status == 3)
                <span class="text-danger">
                    Completada
                </span>
            @endif
        </td>
        <td>
            @if (Carbon\Carbon::parse($apps->appdate) < Carbon\Carbon::today() && $apps->status == 0)
                <a class="common-tbl-btn disabled-btn" href="javascript:void(0)" data-toggle="popover"
                    title="No hay indicaciones"
                    data-content="No hay indicaciones"
                    role="button">No hay indicaciones</a>
            @endif
            @if (Carbon\Carbon::parse($apps->appdate) < Carbon\Carbon::today() && $apps->status == 1)
                <a class="common-tbl-btn disabled-btn" href="javascript:void(0)" data-toggle="popover"
                   title="No hay indicaciones"
                   data-content="No hay indicaciones"
                   role="button">No hay indicaciones</a>
            @endif
            @if ($apps->status == 3)
                <a class="common-tbl-btn disabled-btn" href="javascript:void(0)" data-toggle="popover"
                   title="No hay indicaciones"
                   data-content="No hay indicaciones"
                   role="button">No hay indicaciones</a>
            @endif
            @if ($apps->status == 2)
                <a class="common-tbl-btn view-button " href="#" role="button"
                    title="Ver indicaciones" data-toggle="modal"
                    data-target="#ViewPrescription{{ $apps->id }}">Ver indicaciones</a>
            @endif
        </td>
        <td>
            <div class="create-link-btn">
                @if (Carbon\Carbon::parse($apps->appdate) == Carbon\Carbon::today() && $apps->status == 1 && $apps->type != OFFLINE)
                    @if (hasMeeting($apps->id) == 1)
                        <a class="common-tbl-btn view-button" role="button" data-toggle="modal"
                            title="Ver"
                            data-target="#viewMeetingModal{{ $apps->id }}">Ver</a>
                    @else
                        <a class="common-tbl-btn create-button" role="button" data-toggle="modal"
                            title="Crear"
                            data-target="#createMeetingModal{{ $apps->id }}">Crear</a>
                    @endif
                @else
                    <a class="common-tbl-btn disabled-btn" href="javascript:void(0)"
                        role="button">No hay enlace</a>
                @endif
            </div>
        </td>
        <td>
            <div class="action-btn-wrap">
                {{-- <a class="common-tbl-btn delete-button" title="Delete" href="{{route('delete.appointment', $apps->id)}}"><i class="fas fa-trash-alt"></i></a> --}}
                <div id="heading{{ $apps->id }}">
                    <button class="common-tbl-btn show-icon collapsed" type="button" title="Show Details"
                        data-toggle="collapse" data-target="#collapsedsp{{ $apps->id }}">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </td>
    </tr>
    <tr class="show-details-content">
        <td colspan="6">
            <div id="collapsedsp{{ $apps->id }}" class="collapse" data-parent="#dashboardpagi">
                <div class="card-body d-flex">
                    <div>
                        <div class="show-details-content-item">
                            Paciente<span>{{ isset($apps->patient->name) ? $apps->patient->name : '' }}</span>
                        </div>
                        <div class="show-details-content-item">
                            Fecha<span>{{ $apps->appdate }}</span>
                        </div>
                        <div class="show-details-content-item">
                            Hora<span>{{ $apps->slot ? Carbon\Carbon::parse($apps->slot->start_time)->format('H:i A') : 'N/A' }}-{{ $apps->slot ? Carbon\Carbon::parse($apps->slot->end_time)->format('H:i A') : 'N/A' }}</span>
                        </div>
                        <div class="show-details-content-item">
                            Tipo<span>{{ $apps->type == OFFLINE ? __('main.Offline') : __('main.Online') }}</span>
                        </div>
                        <div class="show-details-content-item">
                            Método de pago<span>{{ $apps->paymentmethod == 'cod' ? 'Spot Payment' : ucfirst($apps->paymentmethod) }}</span>
                        </div>
                        <div class="show-details-content-item">
                            Estado del pago<span
                                class="{{ $apps->is_paid == '1' ? 'text-success' : 'text-danger' }}">{{ $apps->is_paid == '1' ? 'Paid' : 'Unpaid' }}</span>
                        </div>
                        <div class="show-details-content-item">
                            Cargos<span>{{ $apps->fees }}&#36;</span>
                        </div>
                        @if ($apps->type != OFFLINE)
                            @if (hasMeeting($apps->id) == 1)
                                <div class="show-details-content-item">
                                    {{ __('main.Join_URL:') }} <span><a href="{{ $apps->meeting->join_url }}"
                                            title="Join Meeting"
                                            target="_blank">Ir al enlace</a></span>
                                </div>
                                <div class="show-details-content-item">
                                    {{ __('Meeting ID:') }} <span>{{ $apps->meeting->meeting_id }}</span>
                                </div>
                                <div class="show-details-content-item">
                                    Número de reunión <span>{{ $apps->meeting->password }}</span>
                                </div>
                            @else
                                <div class="show-details-content-item">{{ __('Meeting_not_available!') }}</div>
                            @endif
                        @endif
                        <div class="show-details-content-item">
                            {{ __('Comment:') }}<span>{{ $apps->comment }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8">
            <div class='no-appoint-bg'>
                <img class="img-fluid" src="{{ asset('uploaded_file/no-data-patient.svg') }}"
                    alt="{{ __('image') }}">
                <h2 class='no-appoint-title'>No hay citas hoy</h2>
                <a href="{{ redirectDashboard('create-appointment') }}" class='primary-btn'>Crear una nueva cita</a>
            </div>
        </td>
    </tr>
@endforelse
<tr>
    <td colspan='8'>
        {{ view_html($pastapp->links('vendor.pagination.dashboardpatient')) }}
    </td>
</tr>
