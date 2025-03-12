$(document).ready(function() {
    $('#bmiForm').submit(function(e) {
        e.preventDefault();

        var name = $('#name').val().trim();
        var weight = parseFloat($('#weight').val());
        var height = parseFloat($('#height').val());

        if (name === "" || isNaN(weight) || isNaN(height) || weight <= 0 || height <= 0) {
            $('#modalBody').html('Please enter valid values in all fields.');
            $('#resultModal').modal('show');
            return;
        }

        if (weight > 500) {
            $('#modalBody').html('Weight must be less than 500 kg.');
            $('#resultModal').modal('show');
            return;
        }

        if (height > 3) {
            $('#modalBody').html('Height must be less than 3 meters.');
            $('#resultModal').modal('show');
            return;
        }

        $.ajax({
            url: 'calculate.php',
            type: 'POST',
            data: { name: name, weight: weight, height: height },
            dataType: 'json',
            success: function(response) {
                let alertClass, tip;
                if (response.bmi < 18.5) {
                    alertClass = 'alert-info';
                    tip = "Consider a balanced diet to gain healthy weight.";
                } else if (response.bmi < 25) {
                    alertClass = 'alert-success';
                    tip = "Great job! Maintain your healthy lifestyle.";
                } else if (response.bmi < 30) {
                    alertClass = 'alert-warning';
                    tip = "Consider regular exercise to manage your weight.";
                } else {
                    alertClass = 'alert-danger';
                    tip = "Consult a healthcare provider for weight management advice.";
                }
                $('#modalBody').html(`
                    <div class="alert ${alertClass}">${response.message}</div>
                    <p><strong>Health Tip:</strong> ${tip}</p>
                `);
                $('#resultModal').modal('show');

                // history table
                $('#historyBody').empty();
                if (response.history) {
                    response.history.forEach(function(entry) {
                        $('#historyBody').append(`
                            <tr>
                                <td>${entry.name}</td>
                                <td>${entry.bmi.toFixed(2)}</td>
                                <td>${entry.interpretation}</td>
                                <td>${entry.timestamp}</td>
                            </tr>
                        `);
                    });
                }
            },
            error: function() {
                $('#modalBody').html('Server error occurred.');
                $('#resultModal').modal('show');
            }
        });
    });
});
