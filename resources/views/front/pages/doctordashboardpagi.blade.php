@forelse($todayrequest as $apps)
    @if ($apps->paymentmethod != 'Bank' || ($apps->paymentmethod == 'Bank' && $apps->is_paid == 1) || ($apps->paymentmethod == 'cod' && $apps->is_paid == 0))
        <tr>
            <td>
                <div class="user-info">
                    <img class="user-image"
                        src="{{ isset($apps->patient->image) ? asset(path_user_image() . $apps->patient->image) : Avatar::create($apps->patient->name)->toBase64() }}"
                        alt="{{ __('doctor-image') }}" />
                    <h3 class="user-name">{{ $apps->patient->name }}</h3>
                </div>
            </td>
            <td>{{ $apps->appdate }}</td>
            <td>{{ $apps->slot ? Carbon\Carbon::parse($apps->slot->start_time ?? '')->format('H:i A') : 'N/A' }}</td>
            <td>{{ $apps->type == OFFLINE ? __('Offline') : __('Online') }}</td>
            <td><span class="completed">
                    @if ($apps->status == 0)
                        {{ __('Pending') }}
                    @endif
                    @if ($apps->status == 1)
                        {{ __('Cancelled') }}
                    @endif

                    @if ($apps->status == 2)
                        {{ __('Completed') }}
                    @endif
                </span></td>
            <td>
                <div class="action-btn-wrap">
                    @if ($apps->status == 0)
                        <a href="{{ route('approve', $apps->id) }}" title="{{ __('Approve') }}"
                            class="common-tbl-btn show-icon">{{ __('Approve') }}</a>
                    @endif
                    @if ($apps->status != 2)
                        <button title="{{ __('Delete') }}" class="common-tbl-btn delete-button" role="button"
                            data-toggle="modal" data-target="#cancelAppointmentModal{{ $apps->id }}"><i
                                class="fas fa-trash-alt"></i></button>
                    @endif
                    <div id="heading{{ $apps->id }}">
                        <button class="common-tbl-btn show-icon collapsed" type="button" title="Show Details"
                            data-toggle="collapse" data-target="#collapse3{{ $apps->id }}">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="show-details-content">
            <td colspan="8">
                <div id="collapse3{{ $apps->id }}" class="collapse" data-parent="#dashboardpagi">
                    <div class="card-body d-flex">
                        <div>
                            <div class="show-details-content-item">
                                {{ __('Patient:') }}<span>{{ isset($apps->patient->name) ? $apps->patient->name : '' }}</span>
                            </div>
                            <div class="show-details-content-item">
                                {{ __('Appointment Date:') }}<span>{{ $apps->appdate }}</span>
                            </div>
                            <div class="show-details-content-item">
                                {{ __('Appointment Time:') }}<span>{{ $apps->slot ? Carbon\Carbon::parse($apps->slot->start_time)->format('H:i A') : 'N/A' }}-{{ $apps->slot ? Carbon\Carbon::parse($apps->slot->end_time)->format('H:i A') : 'N/A' }}</span>
                            </div>
                            <div class="show-details-content-item">
                                {{ __('Appointment Type:') }}<span>{{ $apps->type == OFFLINE ? __('Offline') : __('Online') }}</span>
                            </div>
                            <div class="show-details-content-item">
                                {{ __('Payment Method:') }}<span>{{ $apps->paymentmethod == 'cod' ? 'Spot Payment' : ucfirst($apps->paymentmethod) }}</span>
                            </div>
                            <div class="show-details-content-item">
                                {{ __('Payment Status:') }}<span
                                    class="{{ $apps->is_paid == '1' ? 'text-success' : 'text-danger' }}">{{ $apps->is_paid == '1' ? 'Paid' : 'Unpaid' }}</span>
                            </div>
                            <div class="show-details-content-item">
                                {{ __('Fees:') }}<span>{{ $apps->fees }}&#36;</span>
                            </div>
                            @if ($apps->type != OFFLINE)
                                @if (hasMeeting($apps->id) == 1)
                                    <div class="show-details-content-item">
                                        {{ __('Join URL:') }} <span><a href="{{ $apps->meeting->join_url }}"
                                                target="_blank">{{ $apps->meeting->join_url }}</a></span>
                                    </div>
                                    <div class="show-details-content-item">
                                        {{ __('Meeting ID:') }} <span>{{ $apps->meeting->meeting_id }}</span>
                                    </div>
                                    <div class="show-details-content-item">
                                        {{ __('Meeting Password:') }}
                                        <span>{{ $apps->meeting->password }}</span>
                                    </div>
                                @else
                                    <div class="show-details-content-item">{{ __('Meeting not available!') }}
                                    </div>
                                @endif
                            @endif
                            <div class="show-details-content-item">
                                {{ __('Comment:') }}<span>{{ $apps->comment }}</span>
                            </div>
                            <div>
                                <form-- action="{{ route('stuff.create_appointment') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="doctor_id"
                                           value="">
                                    <input type="hidden" name="doctorsService"
                                           value="">
                                    <input type="hidden" name="fees"
                                           value="">
                                    <div class="form-group mb-0">
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <div class="input-group">
                                                    <i class="far fa-user"></i>
                                                    <input type="text" class="form-control"
                                                           name="fname"
                                                           placeholder="Historia">
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group mb-0">
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <div class=" mb-0 position-relative">
                                                                    <textarea name="comment" class="stuff-create-appointment-" cols="30" rows="10"
                                                                              placeholder="{{ __('Write something') }}"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit"
                                                    class="btn btn-primary">{{ __('Create Appointment') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </td>
        </tr>
    @endif
@empty
    <tr>
        <td colspan="6">
            <div>
                <img class="img-fluid w-100" src="{{ asset('uploaded_file/no-data.png') }}"
                    alt="{{ __('image') }}">
            </div>
        </td>
    </tr>
@endforelse
