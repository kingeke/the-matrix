$(document).ready(function () {
    $('#file-upload').on('change', function () {
        var fileName = $(this).val();
        $(this).next('.custom-file-label').html(fileName);
    })

    $('.table').each(function () {
        if(!$(this).data('noTable')){
            $(this).dataTable({
                dom: 'lfBrtipHF',
                buttons: [
                    'excel', 'pdf', 'print'
                ],
                paging: true,
                ordering: true,
            })
        }
    })

    $('form').submit(function () {
        let form = $(this)
        let button = form.find('button[type="submit"]')
        buttonLoader(button)
    })

    function buttonLoader(element) {
        element.attr('disabled', 'disabled')
        element.html('<i class="fa fa-spinner fa-spin mr-2"></i> <small>Loading</small>')
    }

    handleCreditType($('#hack-select-dropdown').val());

    $('#hack-select-dropdown').change(function () {
        handleCreditType($(this).val())
    })

    function handleCreditType(val) {

        let creditInput = $('#credit-type-dropdown');

        if (val == 'credit-check') {
            creditInput.parents('.form-group').removeClass('hidden')
            creditInput.attr('required', 'required')
        } else {
            creditInput.parents('.form-group').addClass('hidden')
            creditInput.removeAttr('required')
        }
    }
})