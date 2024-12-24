<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Seat Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .bus-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }
        .seats {
            display: grid;
            grid-template-columns: repeat(4, 50px);
            gap: 10px;
            margin: 20px 0;
        }
        .seat {
            width: 50px;
            height: 50px;
            background-color: #ccc;
            border: 1px solid #999;
            text-align: center;
            line-height: 50px;
            cursor: pointer;
        }
        .seat.selected {
            background-color: #6c5ce7;
            color: white;
        }
        .seat.occupied {
            background-color: #d63031;
            cursor: not-allowed;
        }
        .form-container {
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
        }
        .form-container input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #0984e3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #74b9ff;
        }
        .bill-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .bill-buttons button {
            padding: 10px;
            background-color: #00b894;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .bill-buttons button:hover {
            background-color: #55efc4;
        }
        .back-button {
            margin-top: 20px;
            padding: 10px;
            background-color: #636e72;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #b2bec3;
        }

        .invoice-container {
            font-family: Arial, sans-serif;
            width: 100%;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-header h1 {
            font-size: 24px;
            color: #2d3436;
            margin: 0;
        }
        .invoice-header small {
            font-size: 14px;
            color: #636e72;
        }
        .invoice-body .info p {
            margin: 0;
            font-size: 16px;
            color: #2d3436;
        }
        .invoice-body .info p span {
            font-weight: bold;
            color: #0984e3;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        .invoice-table th {
            background-color: #0984e3;
            color: #fff;
        }
        .invoice-total {
            text-align: right;
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #2d3436;
        }
        .invoice-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #636e72;
        }
    </style>
</head>
<body>
    <div class="bus-container">
        <button class="back-button" onclick="goBack()">← Back</button>
        <h1>Bus Seat Booking</h1>
        <div class="seats" id="seats"></div>
        <form class="form-container" id="bookingForm" method="POST" action="">
            <h2>Passenger Details</h2>
            <label for="name1">Passenger 1 Name:</label>
            <input type="text" name="name1" id="name1" required>
            
            <label for="name2">Passenger 2 Name:</label>
            <input type="text" name="name2" id="name2" required>
            
            <input type="hidden" name="seat1" id="seat1">
            <input type="hidden" name="seat2" id="seat2">
            
            <button type="submit">Confirm Booking</button>
        </form>
        <div class="bill-buttons" id="billButtons" style="display: none;">
            <button id="generateBill1">Download Passenger 1 Invoice</button>
            <button id="generateBill2">Download Passenger 2 Invoice</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
        const seatsContainer = document.getElementById('seats');
        const seat1Input = document.getElementById('seat1');
        const seat2Input = document.getElementById('seat2');
        const billButtons = document.getElementById('billButtons');
        const seatCount = 16; 
        const selectedSeats = [];
        const seatPrice = 20; // Fixed price per seat

        // Generate seats
        for (let i = 1; i <= seatCount; i++) {
            const seat = document.createElement('div');
            seat.classList.add('seat');
            seat.textContent = i;
            seat.dataset.seatId = i;

            seat.addEventListener('click', () => {
                if (!seat.classList.contains('occupied')) {
                    if (selectedSeats.length < 2) {
                        seat.classList.toggle('selected');

                        if (seat.classList.contains('selected')) {
                            selectedSeats.push(i);
                        } else {
                            const index = selectedSeats.indexOf(i);
                            if (index > -1) {
                                selectedSeats.splice(index, 1);
                            }
                        }

                        seat1Input.value = selectedSeats[0] || '';
                        seat2Input.value = selectedSeats[1] || '';
                    } else if (seat.classList.contains('selected')) {
                        seat.classList.remove('selected');
                        const index = selectedSeats.indexOf(i);
                        selectedSeats.splice(index, 1);
                        seat1Input.value = selectedSeats[0] || '';
                        seat2Input.value = selectedSeats[1] || '';
                    } else {
                        alert('You can only select 2 seats!');
                    }
                }
            });

            seatsContainer.appendChild(seat);
        }

        // Enable bill generation
        document.getElementById('bookingForm').addEventListener('submit', (e) => {
            e.preventDefault();
            billButtons.style.display = 'flex';
            alert('Booking confirmed! You can now download invoices.');
        });

        // Generate professional invoice
        function generateInvoice(name, seat, passenger) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const totalAmount = seatPrice;

            doc.setFont('Helvetica', 'bold');
            doc.setFontSize(16);
            doc.text('Bus Booking Invoice', 105, 10, { align: 'center' });

            doc.setFont('Helvetica', 'normal');
            doc.setFontSize(12);
            doc.text(`Date: ${new Date().toLocaleDateString()}`, 10, 30);
            doc.text(`Passenger Name: ${name}`, 10, 40);
            doc.text(`Seat Number: ${seat}`, 10, 50);

            doc.setFont('Helvetica', 'bold');
            doc.text(`Seat Price: $${seatPrice}`, 10, 60);
            doc.text(`Total Amount: $${totalAmount}`, 10, 70);

            doc.setFont('Helvetica', 'italic');
            doc.text('Thank you for traveling with us!', 105, 90, { align: 'center' });

            doc.save(`Passenger${passenger}_Invoice.pdf`);
        }

        document.getElementById('generateBill1').addEventListener('click', () => {
            generateInvoice(document.getElementById('name1').value, seat1Input.value, 1);
        });

        document.getElementById('generateBill2').addEventListener('click', () => {
            generateInvoice(document.getElementById('name2').value, seat2Input.value, 2);
        });

        // Back button functionality
        function goBack() {
            window.location.href = 'HOME.HTML'; // Navigate to the desired page
        }
    </script>
   
</body>
</html>
