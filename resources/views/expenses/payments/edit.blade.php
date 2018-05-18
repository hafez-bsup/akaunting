@extends('layouts.admin')

@section('title', trans('general.title.edit', ['type' => trans_choice('general.payments', 1)]))

@section('content')
    <!-- Default box -->
    <div class="box box-success">
        {!! Form::model($payment, [
            'method' => 'PATCH',
            'files' => true,
            'url' => ['expenses/payments', $payment->id],
            'role' => 'form'
        ]) !!}

        <div class="box-body">
            {{ Form::textGroup('paid_at', trans('general.date'), 'calendar', ['id' => 'paid_at', 'class' => 'form-control', 'required' => 'required', 'data-inputmask' => '\'alias\': \'yyyy-mm-dd\'', 'data-mask' => ''], Date::parse($payment->paid_at)->toDateString()) }}

            {{ Form::textGroup('amount', trans('general.amount'), 'money', ['required' => 'required', 'autofocus' => 'autofocus', 'id' => 'amount', 'class' => 'form-control check-ratio']) }}

            {{ Form::selectGroup('account_id', trans_choice('general.accounts', 1), 'university', $accounts) }}

            {{ Form::textGroup('cad_amount', trans('general.cad_amount'), 'money', ['id' => 'cad_amount', 'class' => 'form-control check-ratio']) }}

            <div class="form-group col-md-6 {{ $errors->has('currency_code') ? 'has-error' : ''}}">
                {!! Form::label('currency_code', trans_choice('general.currencies', 1), ['class' => 'control-label']) !!}
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-exchange"></i></div>
                    {!! Form::text('currency', $currencies[$account_currency_code], ['id' => 'currency', 'class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) !!}
                    {!! Form::hidden('currency_code', $account_currency_code, ['id' => 'currency_code', 'class' => 'form-control', 'required' => 'required']) !!}
                </div>
                {!! $errors->first('currency_code', '<p class="help-block">:message</p>') !!}
            </div>

            <div class="form-group col-md-6 {{ $errors->has('ratio') ? 'has-error' : ''}}">
                {!! Form::label('ratio', trans_choice('general.ratio', 1), ['class' => 'control-label']) !!}
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-money"></i></div>
                    {!! Form::text('ratio', null, ['id' => 'ratio', 'class' => 'form-control check-ratio', 'placeholder' => trans('general.ratio')]) !!}
                </div>
                <p class="help-block">
                    <span class="check-ratio-warning text-danger">
                        {{ trans('messages.warning.check_ratio') }}
                    </span>
                </p>
            </div>

            {{ Form::textareaGroup('description', trans('general.description')) }}

            {{ Form::selectGroup('category_id', trans_choice('general.categories', 1), 'folder-open-o', $categories) }}

            {{ Form::selectGroup('vendor_id', trans_choice('general.vendors', 1), 'user', $vendors, null, []) }}

            {{ Form::selectGroup('payment_method', trans_choice('general.payment_methods', 1), 'credit-card', $payment_methods) }}

            {{ Form::textGroup('reference', trans('general.reference'), 'file-text-o',[]) }}

            {{ Form::fileGroup('attachment', trans('general.attachment')) }}
        </div>
        <!-- /.box-body -->

        @permission('update-expenses-payments')
        <div class="box-footer">
            {{ Form::saveButtons('expenses/payments') }}
        </div>
        <!-- /.box-footer -->
        @endpermission
    </div>

      {!! Form::close() !!}
@endsection

@push('js')
<script src="{{ asset('vendor/almasaeed2010/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('public/js/bootstrap-fancyfile.js') }}"></script>
@endpush

@push('css')
<link rel="stylesheet" href="{{ asset('vendor/almasaeed2010/adminlte/plugins/datepicker/datepicker3.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/bootstrap-fancyfile.css') }}">
@endpush

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        //Date picker
        $('#paid_at').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });

        $("#account_id").select2({
            placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.accounts', 1)]) }}"
        });

        $("#category_id").select2({
            placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.categories', 1)]) }}"
        });

        $("#vendor_id").select2({
            placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.vendors', 1)]) }}"
        });

        $("#payment_method").select2({
            placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.payment_methods', 1)]) }}"
        });

        $('#attachment').fancyfile({
            text  : '{{ trans('general.form.select.file') }}',
            style : 'btn-default',
            @if($payment->attachment)
            placeholder : '<?php echo $payment->attachment->basename; ?>'
            @else
            placeholder : '{{ trans('general.form.no_file_selected') }}'
            @endif
        });

        @if($payment->attachment)
            attachment_html  = '<span class="attachment">';
            attachment_html += '    <a href="{{ url('uploads/' . $payment->attachment->id . '/download') }}">';
            attachment_html += '        <span id="download-attachment" class="text-primary">';
            attachment_html += '            <i class="fa fa-file-{{ $payment->attachment->aggregate_type }}-o"></i> {{ $payment->attachment->basename }}';
            attachment_html += '        </span>';
            attachment_html += '    </a>';
            attachment_html += '    {!! Form::open(['id' => 'attachment-' . $payment->attachment->id, 'method' => 'DELETE', 'url' => [url('uploads/' . $payment->attachment->id)], 'style' => 'display:inline']) !!}';
            attachment_html += '    <a id="remove-attachment" href="javascript:void();">';
            attachment_html += '        <span class="text-danger"><i class="fa fa fa-times"></i></span>';
            attachment_html += '    </a>';
            attachment_html += '    {!! Form::close() !!}';
            attachment_html += '</span>';

            $('.fancy-file .fake-file').append(attachment_html);

            $(document).on('click', '#remove-attachment', function (e) {
                confirmDelete("#attachment-{!! $payment->attachment->id !!}", "{!! trans('general.attachment') !!}", "{!! trans('general.delete_confirm', ['name' => '<strong>' . $payment->attachment->basename . '</strong>', 'type' => strtolower(trans('general.attachment'))]) !!}", "{!! trans('general.cancel') !!}", "{!! trans('general.delete')  !!}");
            });
        @endif

        $(document).on('change', '#account_id', function (e) {
            $.ajax({
                url: '{{ url("settings/currencies/currency") }}',
                type: 'GET',
                dataType: 'JSON',
                data: 'account_id=' + $(this).val(),
                success: function(data) {
                    $('#currency').val(data.currency_name);
                    $('#currency_code').val(data.currency_code);
                }
            });
        });

        $(document).on('input', '.check-ratio', function (e) {
            var cad_amount = +$('#cad_amount').val();
            var real_amount = +$('#amount').val() / cad_amount;
            var ratio = +$('#ratio').val();

            if(cad_amount && (real_amount > ratio))
            {
                $(".check-ratio-warning").text('{{ trans('messages.warning.check_ratio') }}'.replace(":x", real_amount.toFixed(2))).show();
            }
            else {
                $('.check-ratio-warning').hide();
            }
        });

        $('.check-ratio').trigger('input');
    });
</script>
@endpush
