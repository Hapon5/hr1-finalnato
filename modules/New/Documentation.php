<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgb(243, 161, 9);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: Navy;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="date"], input[type="number"], select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .file-upload-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        input[type="file"] {
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            background-color: #ffffff;
            border: 2px solid #ddd;
            border-radius: 5px;
            padding: 8px 12px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s ease;
            width: calc(100% - 20px);
        }

        .file-upload-wrapper:hover {
            border-color: #d37a15;
        }


        .file-upload-text {
            margin-right: 8px;
             font-size: 14px;
        }

        .file-upload-button {
            background-color: #d37a15;
            color: rgb(255, 255, 255);
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
            white-space: nowrap;
        }


        button {
            background-color: #d37a15;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto 0 auto;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #d37a15;
        }
        .status {
            font-weight: bold;
            margin-left: 10px;
        }
        .status.pending {
            color: orange;
        }
        .status.verified {
            color: green;
        }

        .back-button {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 30px;
            top: 30px;
            z-index: 20;
            text-decoration: none;
        }

        .back-button:hover {
            background: #c0392b;
        }

    </style>
</head>


   
  </div>

    <div class="main-content">
        <div class="container">
            <h1>New Hired Documentation</h1>
            <form id="documentationForm">
                <div class="form-group">
                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" required>
                </div>
                <div class="form-group">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" required>
                </div>
                <div class="form-group">
                    <label for="middleName">Middle Name:</label>
                    <input type="text" id="middleName" name="middleName">
                </div>
                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" required>
                </div>
                <div class="form-group">
                    <label for="birthDate">Birth Date:</label>
                    <input type="date" id="birthDate" name="birthDate" required>
                </div>
                <div class="form-group">
                    <label for="idUpload">Upload ID:</label>
                    <div class="file-upload-group">
                        <div class="file-upload-wrapper">
                            <span class="file-upload-text">Choose File</span>
                            <span class="file-upload-button">Browse</span>
                        </div>
                        <input type="file" id="idUpload" name="idUpload" accept="image/*,.pdf" required>
                        <span id="idStatus" class="status pending">Pending</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="certificatesUpload">Upload Certificates:</label>
                     <div class="file-upload-group">
                        <div class="file-upload-wrapper">
                            <span class="file-upload-text">Choose File</span>
                            <span class="file-upload-button">Browse</span>
                        </div>
                        <input type="file" id="certificatesUpload" name="certificatesUpload" accept="image/*,.pdf" multiple>
                         <span id="certificatesStatus" class="status pending">Pending</span>
                    </div>
                </div>
                <button type="submit">Submit Documents</button>
            </form>
        </div>
    </div>

    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }
        function toggleSubMenu(id) {
            const submenu = document.getElementById(id);
            if (submenu.style.display === 'none') {
                submenu.style.display = 'block';
            } else {
                submenu.style.display = 'none';
            }
        }

        const documentationForm = document.getElementById('documentationForm');
        const idUploadInput = document.getElementById('idUpload');
        const certificatesUploadInput = document.getElementById('certificatesUpload');
        const idStatusSpan = document.getElementById('idStatus');
        const certificatesStatusSpan = document.getElementById('certificatesStatus');
        const fileUploadWrappers = document.querySelectorAll('.file-upload-wrapper');
        const fileUploadTexts = document.querySelectorAll('.file-upload-text');


        idUploadInput.addEventListener('change', () => {
            if (idUploadInput.files.length > 0) {
                fileUploadTexts[0].textContent = idUploadInput.files[0].name;
                idStatusSpan.textContent = 'Uploaded';
                idStatusSpan.className = 'status verified';
            } else {
                fileUploadTexts[0].textContent = 'Choose File';
                idStatusSpan.textContent = 'Pending';
                idStatusSpan.className = 'status pending';
            }
        });

       certificatesUploadInput.addEventListener('change', () => {
            if (certificatesUploadInput.files.length > 0) {
                  let fileNames = Array.from(certificatesUploadInput.files).map(file => file.name).join(', ');
                fileUploadTexts[1].textContent = fileNames;
                certificatesStatusSpan.textContent = 'Uploaded';
                certificatesStatusSpan.className = 'status verified';
            } else {
                fileUploadTexts[1].textContent = 'Choose File';
                certificatesStatusSpan.textContent = 'Pending';
                certificatesStatusSpan.className = 'status pending';
            }
        });



        documentationForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const lastName = document.getElementById('lastName').value;
            const firstName = document.getElementById('firstName').value;
            const middleName = document.getElementById('middleName').value;
            const age = document.getElementById('age').value;
            const birthDate = document.getElementById('birthDate').value;

            if (idUploadInput.files.length === 0 || certificatesUploadInput.files.length === 0) {
                alert('Please upload all required documents.');
                return;
            }
             if (!/^\d+$/.test(age) || parseInt(age) <= 0) {
                alert('Please enter a valid age.');
                return;
            }

            console.log('Last Name:', lastName);
            console.log('First Name:', firstName);
            console.log('Middle Name:', middleName);
            console.log('Age:', age);
            console.log('Birth Date:', birthDate);
            console.log('ID Upload:', idUploadInput.files[0]);
            console.log('Certificates Upload:', certificatesUploadInput.files);

            alert('Documents submitted successfully!');
            documentationForm.reset();
             fileUploadTexts[0].textContent = 'Choose File';
             fileUploadTexts[1].textContent = 'Choose File';
            idStatusSpan.textContent = 'Pending';
            idStatusSpan.className = 'status pending';
            certificatesStatusSpan.textContent = 'Pending';
            certificatesStatusSpan.className = 'status pending';

        });
    </script>
</body>
</html>

