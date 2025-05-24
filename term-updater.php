<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Value Transfer Tool</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for the modal overlay to control visibility and positioning */
        .modal-overlay {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            top: 0;
            left: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
            justify-content: center; /* Center content horizontally */
            align-items: center; /* Center content vertically */
            z-index: 50; /* High z-index to appear on top of other content */
        }

        /* Styles for the modal content box */
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 0.5rem; /* Rounded corners */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Shadow for depth */
            position: relative; /* Needed for positioning the close button */
            max-width: 90%; /* Max width for responsiveness */
            width: 400px; /* Fixed width for larger screens */
            text-align: center; /* Center text inside modal */
        }

        /* Styles for the modal close button */
        .modal-close-button {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            font-size: 1.25rem;
            cursor: pointer;
            color: gray;
        }

        /* Keyframes for a simple spin animation for the loader */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* Class to apply the spin animation */
        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Custom styles for the export table container */
        .export-table-container {
            height: 100%; /* Ensure it takes full height of its flex parent */
            overflow-y: auto; /* Enable vertical scrolling */
            border: 1px solid #e2e8f0; /* Light gray border */
            border-radius: 0.5rem; /* Rounded corners */
            background-color: #f9fafb; /* Light background */
        }

        /* Styles for the table itself */
        .export-table {
            width: 100%; /* Full width within its container */
            border-collapse: collapse; /* Collapse borders between cells */
        }

        .export-table th, .export-table td {
            padding: 0.75rem; /* Padding for cells */
            text-align: left; /* Align text to the left */
            border-bottom: 1px solid #e2e8f0; /* Bottom border for rows */
        }

        .export-table th {
            background-color: #edf2f7; /* Slightly darker background for headers */
            font-weight: 600; /* Semi-bold font for headers */
            color: #4a5568; /* Darker text for headers */
            position: sticky; /* Sticky header for scrolling content */
            top: 0;
            z-index: 10; /* Ensure header stays on top */
        }

        .export-table tr:last-child td {
            border-bottom: none; /* No bottom border for the last row */
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen font-sans">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-6xl mx-4 my-8 flex flex-col md:flex-row gap-8 max-h-[calc(80vh-4rem)] overflow-y-auto">

        <div class="flex-1 flex flex-col justify-between">
            <div class="mb-6">
                <label for="inputBox" class="block text-gray-700 text-sm font-semibold mb-2">Enter Value:</label>
                <input type="text" id="inputBox"
                       class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200 ease-in-out"
                       placeholder="Type something here...">
                <div id="fetchedDataDisplay" class="mt-2 p-2 border border-gray-200 rounded-md bg-gray-50 text-gray-800 break-words min-h-[2.5rem] flex items-center">
                    </div>
            </div>
            <button id="fetchDataButton" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-opacity-75 transition duration-200 ease-in-out mb-4 w-full flex items-center justify-center">
                <svg id="fetchSpinner" class="animate-spin h-5 w-5 text-white mr-3 hidden" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="fetchButtonText">Fetch Data</span>
            </button>

            <button id="transferButton"
                    class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-opacity-75 transition duration-200 ease-in-out mb-4 w-full">
                Transfer Value
            </button>

            <button id="exportExcelButton"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-75 transition duration-200 ease-in-out w-full">
                Export to Excel
            </button>

            <div class="flex justify-center space-x-4 mt-auto pt-6"> <button id="undoButton"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-75 transition duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    &#8678; Undo
                </button>
                <button id="redoButton"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-75 transition duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    &#8680; Redo
                </button>
            </div>
        </div>

        <div class="flex-1 flex flex-col">
            <div class="flex-grow flex flex-col mb-6">
                <label for="outputTextArea" class="block text-gray-700 text-sm font-semibold mb-2">Output Textarea:</label>
                <textarea id="outputTextArea" readonly
                          class="shadow-sm appearance-none border border-gray-300 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-transparent resize-y flex-1 min-h-[10rem] transition duration-200 ease-in-out"
                          placeholder="Transferred values will appear here."></textarea>
                <p class="text-gray-600 text-sm mt-2">Value Count: <span id="valueCount" class="font-semibold">0</span></p>
            </div>
        </div>

        <div class="flex-1 flex flex-col">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Export Data Preview:</label>
            <div id="exportTableContainer" class="export-table-container flex-grow">
                <table id="exportTable" class="export-table">
                    <thead>
                        <tr>
                            <th>Entered Value</th>
                            <th>Transferred Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="feedbackModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close-button" onclick="hideModal()">&times;</span>
            <p id="modalMessage" class="text-lg font-medium text-gray-800 mb-4"></p>
            <button onclick="hideModal()"
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-75 transition duration-200 ease-in-out">
                Close
            </button>
        </div>
    </div>

    <script>
        // Get references to all necessary DOM elements
        const inputBox = document.getElementById('inputBox');
        const fetchDataButton = document.getElementById('fetchDataButton');
        const fetchButtonText = document.getElementById('fetchButtonText');
        const fetchSpinner = document.getElementById('fetchSpinner');
        const transferButton = document.getElementById('transferButton');
        const outputTextArea = document.getElementById('outputTextArea');
        const valueCountSpan = document.getElementById('valueCount');
        const undoButton = document.getElementById('undoButton');
        const redoButton = document.getElementById('redoButton');
        const feedbackModal = document.getElementById('feedbackModal');
        const modalMessage = document.getElementById('modalMessage');
        const fetchedDataDisplay = document.getElementById('fetchedDataDisplay');
        const exportExcelButton = document.getElementById('exportExcelButton');
        const exportTableContainer = document.getElementById('exportTableContainer'); // New container reference
        const exportTableBody = document.querySelector('#exportTable tbody'); // New table body reference

        // Initialize history array and index for undo/redo functionality.
        // Each history state now includes both the textarea content and the transfer log.
        let history = [];
        let historyIndex = -1;
        let lastFetchedInput = ''; // Stores the input box value that triggered the last fetch
        let currentTransferLog = []; // Stores the log of transfers for the current state (for Excel export)

        /**
         * Updates the displayed count of values in the output textarea.
         * Values are considered separated by ',\n'.
         */
        function updateValueCount() {
            // Split the textarea content by ',\n' to get individual values.
            // If the textarea is empty, the split will result in [''], so we handle that.
            const values = outputTextArea.value.trim() === '' ? [] : outputTextArea.value.trim().split(',\n');
            valueCountSpan.textContent = values.length;
        }

        /**
         * Saves the current state of the outputTextArea and the currentTransferLog
         * to the history array. If the user has undone actions and then adds a new value,
         * all "future" history states are discarded.
         */
        function saveHistory() {
            // If we are not at the end of the history (meaning user has undone actions),
            // truncate the history to the current point before adding a new state.
            if (historyIndex < history.length - 1) {
                history = history.slice(0, historyIndex + 1);
            }
            // Add the current textarea value and a deep copy of the currentTransferLog to the history
            history.push({
                outputTextAreaContent: outputTextArea.value,
                transferLogState: JSON.parse(JSON.stringify(currentTransferLog)) // Deep copy to prevent mutation
            });
            // Move the history index to the newly added state
            historyIndex++;
            // Update the disabled state of undo/redo buttons
            updateUndoRedoButtons();
            // Update the export table display
            updateExportTable();
        }

        /**
         * Reverts the outputTextArea and currentTransferLog to the previous state in the history.
         * Disables the undo button if at the beginning of history.
         */
        function undo() {
            // Check if there's a previous state to go back to
            if (historyIndex > 0) {
                historyIndex--; // Decrement the index
                outputTextArea.value = history[historyIndex].outputTextAreaContent; // Set textarea to the previous state
                currentTransferLog = JSON.parse(JSON.stringify(history[historyIndex].transferLogState)); // Restore transfer log
                updateValueCount(); // Update the value count display
                updateUndoRedoButtons(); // Update button states
                updateExportTable(); // Update the export table display
            }
        }

        /**
         * Advances the outputTextArea and currentTransferLog to the next state in the history.
         * Disables the redo button if at the latest state.
         */
        function redo() {
            // Check if there's a future state to go forward to
            if (historyIndex < history.length - 1) {
                historyIndex++; // Increment the index
                outputTextArea.value = history[historyIndex].outputTextAreaContent; // Set textarea to the next state
                currentTransferLog = JSON.parse(JSON.stringify(history[historyIndex].transferLogState)); // Restore transfer log
                updateValueCount(); // Update the value count display
                updateUndoRedoButtons(); // Update button states
                updateExportTable(); // Update the export table display
            }
        }

        /**
         * Updates the disabled property of the Undo and Redo buttons
         * based on the current position in the history array.
         */
        function updateUndoRedoButtons() {
            // Undo button is disabled if at the very first state (index 0) or before any state (-1)
            undoButton.disabled = historyIndex <= 0;
            // Redo button is disabled if at the last state in history
            redoButton.disabled = historyIndex >= history.length - 1;
        }

        /**
         * Displays the modal with a given message.
         * @param {string} message - The message to display in the modal.
         */
        function showModal(message) {
            modalMessage.textContent = message; // Set the message text
            feedbackModal.style.display = 'flex'; // Make the modal visible by changing display to flex
        }

        /**
         * Hides the modal.
         */
        function hideModal() {
            feedbackModal.style.display = 'none'; // Hide the modal by changing display to none
        }

        /**
         * Fetches data from a PHP endpoint using AJAX (POST request).
         * It sends the value from inputBox as 'term_id' and expects a JSON response
         * with 'term_id', 'name', and 'description'.
         * The fetched data is then displayed in the new fetchedDataDisplay div.
         */
        async function fetchDataFromPHP() {
            const inputValue = inputBox.value.trim();
            if (inputValue === '') {
                showModal('Please enter a value to fetch data.');
                return;
            }

            fetchDataButton.disabled = true; // Disable button during fetch
            fetchButtonText.textContent = 'Fetching...'; // Show loading text
            fetchSpinner.classList.remove('hidden'); // Show spinner
            inputBox.placeholder = 'Fetching data...'; // Update input placeholder

            // Store the original input value before clearing for potential transfer logging
            lastFetchedInput = inputValue;

            try {
                const formData = new FormData();
                formData.append('term_id', inputValue); // Send current input box value as term_id

                // Make the POST request to the PHP endpoint
                const response = await fetch('update.php', {
                    method: 'POST',
                    body: formData
                });

                // Check if the network response was successful
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }

                // Parse the JSON response
                const data = await response.json();

                // Assuming the PHP returns JSON with 'term_id', 'name', and 'description'
                if (data.term_id && data.name && data.description) {
                    // Format the output string
                    const output = `Name: ${data.name}\nDescription: ${data.description}`;
                    // Display the fetched result in the new fetchedDataDisplay div
                    fetchedDataDisplay.textContent = output;
                    inputBox.value = ''; // Clear the input box after fetching
                } else {
                    // Show a modal if the fetched data structure is not as expected
                    showModal('Failed to fetch valid term data. Response missing expected fields.');
                    fetchedDataDisplay.textContent = 'No valid data fetched.'; // Indicate no valid data
                    inputBox.value = ''; // Clear input if data is invalid
                    lastFetchedInput = ''; // Clear last fetched input if data is invalid
                }

            } catch (error) {
                // Catch and display any errors during the fetch operation
                showModal("An error occurred while fetching data. Check console for details.");
                console.error('Fetch Data Error:', error);
                fetchedDataDisplay.textContent = 'Error fetching data.'; // Indicate error
                inputBox.value = ''; // Clear input on error
                lastFetchedInput = ''; // Clear last fetched input on error
            } finally {
                // Re-enable the button and reset its text and input placeholder
                fetchDataButton.disabled = false;
                fetchButtonText.textContent = 'Fetch Data';
                fetchSpinner.classList.add('hidden'); // Hide spinner
                inputBox.placeholder = 'Type something here...';
            }
        }

        /**
         * Exports the current transfer log to a CSV file, which can be opened in Excel.
         * Only includes entries where both entered and transferred values are present.
         */
        function exportToExcel() {
            if (currentTransferLog.length === 0) {
                showModal('No data to export. Please transfer some values first.');
                return;
            }

            let csvContent = "Entered Value,Transferred Value\n"; // CSV header

            // Filter and format data from the currentTransferLog
            currentTransferLog.forEach(entry => {
                if (entry.enteredValue && entry.transferredValue) {
                    // Escape double quotes in values and wrap them in double quotes
                    const escapedEntered = `"${entry.enteredValue.replace(/"/g, '""')}"`;
                    const escapedTransferred = `"${entry.transferredValue.replace(/"/g, '""')}"`;
                    csvContent += `${escapedEntered},${escapedTransferred}\n`;
                }
            });

            // Create a Blob from the CSV content
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

            // Create a temporary anchor element
            const link = document.createElement('a');
            if (link.download !== undefined) { // Feature detection for download attribute
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'value_transfers.csv');
                link.style.visibility = 'hidden'; // Hide the link
                document.body.appendChild(link); // Append to body
                link.click(); // Programmatically click the link to trigger download
                document.body.removeChild(link); // Clean up
                URL.revokeObjectURL(url); // Release the object URL
            } else {
                showModal('Your browser does not support downloading files directly. Please copy the data manually.');
            }
        }

        /**
         * Updates the tabular display of the transfer log in the third section.
         * Scrolls to the latest entry.
         */
        function updateExportTable() {
            exportTableBody.innerHTML = ''; // Clear existing rows

            currentTransferLog.forEach(entry => {
                // Only add entries that have both entered and transferred values
                if (entry.enteredValue && entry.transferredValue) {
                    const row = exportTableBody.insertRow();
                    const cell1 = row.insertCell();
                    const cell2 = row.insertCell();
                    cell1.textContent = entry.enteredValue;
                    cell2.textContent = entry.transferredValue;
                }
            });

            // Scroll to the bottom to show the latest entry
            exportTableContainer.scrollTop = exportTableContainer.scrollHeight;
        }


        // Event listener for the "Fetch Data" button
        fetchDataButton.addEventListener('click', fetchDataFromPHP);

        // Event listener for paste event on inputBox to trigger fetch
        inputBox.addEventListener('paste', (event) => {
            // Give a small delay to allow the pasted content to fully register in the input field
            setTimeout(() => {
                fetchDataFromPHP();
            }, 0);
        });

        // Event listener for "Enter" key press on inputBox to trigger fetch
        inputBox.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                fetchDataFromPHP();
            }
        });

        // Event listener for the "Transfer Value" button
        transferButton.addEventListener('click', () => {
            let valueToTransfer = fetchedDataDisplay.textContent.trim();
            let originalInputForLog = '';

            // Determine the original input value for the log
            if (valueToTransfer !== '') { // Data was fetched and is now being transferred
                originalInputForLog = lastFetchedInput;
            } else { // Direct transfer from inputBox
                valueToTransfer = inputBox.value.trim();
                originalInputForLog = inputBox.value.trim();
            }

            if (valueToTransfer !== '') {
                // If the outputTextArea already has content, append with ',\n'
                if (outputTextArea.value !== '') {
                    outputTextArea.value += ',\n' + valueToTransfer;
                } else {
                    // If it's empty, just set the value
                    outputTextArea.value = valueToTransfer;
                }
                // Add the transfer record to the current log
                currentTransferLog.push({ enteredValue: originalInputForLog, transferredValue: valueToTransfer });

                inputBox.value = ''; // Clear the input box
                fetchedDataDisplay.textContent = ''; // Clear the fetched data display
                lastFetchedInput = ''; // Clear the last fetched input after transfer

                saveHistory(); // Save the new state to history (includes transfer log and updates table)
                updateValueCount(); // Update the displayed value count
            } else {
                // If both input and fetched display are empty, show a feedback modal
                showModal('Please enter a value or fetch data before transferring.');
            }
        });

        // Event listeners for the Undo and Redo buttons
        undoButton.addEventListener('click', undo);
        redoButton.addEventListener('click', redo);

        // Event listener for the new "Export to Excel" button
        exportExcelButton.addEventListener('click', exportToExcel);

        // Event listener to close modal when clicking outside its content
        feedbackModal.addEventListener('click', (event) => {
            if (event.target === feedbackModal) {
                hideModal();
            }
        });

        // Initial setup when the page loads:
        // 1. Save the initial (empty) state to history so undo works from the start.
        saveHistory();
        // 2. Update the undo/redo button states based on the initial history.
        updateUndoRedoButtons();
        // 3. Initial update of the export table
        updateExportTable();
    </script>
</body>
</html>
