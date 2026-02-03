<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    <!-- Formula Parser (HyperFormula) -->
    <script src="https://cdn.jsdelivr.net/npm/hyperformula@2.6.2/dist/hyperformula.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.2/papaparse.min.js"></script>

    <script>
        // Show loading indicator
        function showLoading(step) {
            document.getElementById(`import-loading-${step}`).classList.remove('hidden');
        }

        function hideLoading(step) {
            document.getElementById(`import-loading-${step}`).classList.add('hidden');
        }

        // Handle file import (Excel/CSV)
        function handleFileImport(event, step) {
            const file = event.target.files[0];
            if (!file) return;

            showLoading(step);

            const reader = new FileReader();
            const fileName = file.name.toLowerCase();

            reader.onload = function(e) {
                try {
                    let data;

                    if (fileName.endsWith('.csv')) {
                        // Parse CSV
                        Papa.parse(file, {
                            complete: function(results) {
                                data = results.data;
                                importDataToSpreadsheet(data, step);
                            },
                            error: function(error) {
                                console.error('CSV parsing error:', error);
                                alert('Error parsing CSV file: ' + error.message);
                                hideLoading(step);
                            }
                        });
                    } else {
                        // Parse Excel
                        const workbook = XLSX.read(e.target.result, { type: 'binary' });
                        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                        data = XLSX.utils.sheet_to_json(firstSheet, { header: 1, defval: '' });
                        importDataToSpreadsheet(data, step);
                    }
                } catch (error) {
                    console.error('Import error:', error);
                    alert('Error importing file: ' + error.message);
                    hideLoading(step);
                }
            };

            if (fileName.endsWith('.csv')) {
                reader.readAsText(file);
            } else {
                reader.readAsBinaryString(file);
            }

            // Reset file input
            event.target.value = '';
        }

        // Import from Google Sheets
        function importFromGoogleSheets(step) {
            document.getElementById(`google-sheets-input-${step}`).classList.remove('hidden');
        }

        function cancelGoogleSheets(step) {
            document.getElementById(`google-sheets-input-${step}`).classList.add('hidden');
            document.getElementById(`sheets-url-${step}`).value = '';
        }

        async function loadGoogleSheet(step) {
            const url = document.getElementById(`sheets-url-${step}`).value.trim();
            
            if (!url) {
                alert('Please enter a Google Sheets URL');
                return;
            }

            // Extract spreadsheet ID from URL
            const match = url.match(/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/);
            if (!match) {
                alert('Invalid Google Sheets URL');
                return;
            }

            const spreadsheetId = match[1];
            
            // Try to get sheet name from URL or use default
            let sheetName = 'Sheet1';
            const gidMatch = url.match(/[#&]gid=([0-9]+)/);
            
            showLoading(step);

            try {
                // Use Google Sheets API via public CSV export
                const csvUrl = `https://docs.google.com/spreadsheets/d/${spreadsheetId}/export?format=csv${gidMatch ? '&gid=' + gidMatch[1] : ''}`;
                
                const response = await fetch(csvUrl);
                
                if (!response.ok) {
                    throw new Error('Unable to access Google Sheet. Make sure it is publicly accessible.');
                }

                const csvText = await response.text();
                
                Papa.parse(csvText, {
                    complete: function(results) {
                        importDataToSpreadsheet(results.data, step);
                        cancelGoogleSheets(step);
                    },
                    error: function(error) {
                        console.error('CSV parsing error:', error);
                        alert('Error parsing Google Sheet data: ' + error.message);
                        hideLoading(step);
                    }
                });
            } catch (error) {
                console.error('Google Sheets import error:', error);
                alert(error.message);
                hideLoading(step);
            }
        }

        // Import data into Handsontable
        function importDataToSpreadsheet(data, step) {
            try {
                if (!data || data.length === 0) {
                    alert('No data found in the file');
                    hideLoading(step);
                    return;
                }

                // Get current data dimensions
                const currentData = hot.getData();
                const currentRows = currentData.length;
                const currentCols = currentData[0].length;

                // Ensure imported data fits
                const importRows = data.length;
                const importCols = Math.max(...data.map(row => row.length));

                // Pad rows if needed
                if (importRows > currentRows) {
                    const rowsToAdd = importRows - currentRows;
                    for (let i = 0; i < rowsToAdd; i++) {
                        data.push(Array(importCols).fill(''));
                    }
                }

                // Pad columns in each row if needed
                const paddedData = data.map(row => {
                    const newRow = [...row];
                    while (newRow.length < currentCols) {
                        newRow.push('');
                    }
                    return newRow.slice(0, currentCols); // Trim to current column count
                });

                // Load data into Handsontable
                hot.loadData(paddedData);

                hideLoading(step);
                
                // Show success message
                showSuccessMessage('Data imported successfully!');
            } catch (error) {
                console.error('Error importing data:', error);
                alert('Error importing data: ' + error.message);
                hideLoading(step);
            }
        }

        // Download template
        function downloadTemplate(step) {
            let templateData;
            
            // Create workbook
            const wb = XLSX.utils.book_new();
            
            // Step-specific template structures
            switch(step) {
                case 1: // Journal Entries - with nested headers
                    // Get the actual structure from Handsontable
                    templateData = [
                        // First header row - merged cells
                        ['', 'ASSETS', '', '', '', '', '', 'LIABILITIES', '', "OWNER'S EQUITY", '', '', 'EXPENSES', '', '', ''],
                        // Second header row - individual column names
                        ['', 'Cash', 'Accounts Receivable', 'Supplies', 'Furniture & Fixtures', 'Land', 'Equipment', 
                        'Accounts Payable', 'Notes Payable', 'Capital', 'Withdrawal', 'Service Revenue', 
                        'Rent Expense', 'Utilities Expense', 'Salaries Expense', 'Misc. Expense'],
                        // Empty data rows for users to fill
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']
                    ];
                    break;
                    
                case 2: // Journalizing Entries
                case 5: // Adjusting Entries
                    templateData = [
                        ['Date', '', 'Account Titles and Explanation', 'Account Number', 'Debit (₱)', 'Credit (₱)'],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', '']
                    ];
                    break;
                    
                case 3: // T-Accounts
                    const accounts = [
                        'Cash', 'Accounts Receivable', 'Supplies', 'Furniture & Fixture',
                        'Land', 'Equipment', 'Accumulated Depreciation - F&F',
                        'Accumulated Depreciation - Equipment', 'Accounts Payable', 'Notes Payable', 
                        'Utilities Payable', 'Capital', 'Withdrawals', 'Service Revenue', 
                        'Rent Expense', 'Utilities Expense', 'Salaries Expense', 'Supplies Expense', 
                        'Depreciation Expense', 'Income Summary'
                    ];
                    
                    // First row: Account names (merged across their sections)
                    const row1 = [];
                    const row2 = ['Date', '', 'Debit (₱)', 'Credit (₱)', '', 'Date']; // Pattern for each account
                    accounts.forEach(account => {
                        row1.push(account, '', '', '', '', '');
                    });
                    
                    // Second row: Column headers repeated for each account
                    const fullRow2 = [];
                    accounts.forEach(() => {
                        fullRow2.push(...row2);
                    });
                    
                    templateData = [row1, fullRow2];
                    
                    // Add empty data rows
                    for (let i = 0; i < 10; i++) {
                        templateData.push(Array(accounts.length * 6).fill(''));
                    }
                    break;
                    
                case 4: // Trial Balance
                case 9: // Closing Entries  
                    templateData = [
                        ['Durano Enterprise', '', ''],
                        [step === 4 ? 'Trial Balance' : 'Closing Entries', '', ''],
                        ['Date: ____________', '', ''],
                        ['Account Title', 'Debit (₱)', 'Credit (₱)'],
                        ['', '', ''],
                        ['', '', ''],
                        ['', '', ''],
                        ['', '', ''],
                        ['', '', ''],
                        ['', '', ''],
                        ['', '', ''],
                        ['', '', ''],
                        ['', '', ''],
                        ['', '', '']
                    ];
                    break;
                    
                case 6: // Worksheet
                    templateData = [
                        ['Durano Enterprise', '', '', '', '', '', '', '', '', '', ''],
                        ['Worksheet', '', '', '', '', '', '', '', '', '', ''],
                        ['Date: ____________________________', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', ''],
                        ['Account Title', 'Unadjusted Trial Balance', '', 'Adjustments', '', 'Adjusted Trial Balance', '', 'Income Statement', '', 'Balance Sheet', ''],
                        ['', 'Debit', 'Credit', 'Debit', 'Credit', 'Debit', 'Credit', 'Debit', 'Credit', 'Debit', 'Credit'],
                        ['', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '']
                    ];
                    break;
                    
                case 7: // Financial Statements
                    templateData = [
                        ['Durano Enterprise', '', '', '', 'Durano Enterprise', '', '', '', 'Durano Enterprise', '', '', ''],
                        ['Income Statement', '', '', '', 'Statement of Changes in Equity', '', '', '', 'Balance Sheet', '', '', ''],
                        ['For the month ended February 29, 2024', '', '', '', 'For the month ended February 29, 2024', '', '', '', 'As of February 29, 2024', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', ''],
                        ['Revenues:', '', '', '', 'Durano, Capital, beginning', '', '', '', 'Assets', '', '', ''],
                        ['Service Revenue', '', '', '', 'Add: Investment', '', '', '', 'Current assets', '', '', ''],
                        ['', '', '', '', '          Net Income', '', '', '', 'Cash', '', '', ''],
                        ['Less: Expenses', '', '', '', 'Total', '', '', '', 'Accounts receivable', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', ''],
                        ['', '', '', '', '', '', '', '', '', '', '', '']
                    ];
                    break;
                    
                case 8: // Balance Sheet (same as step 2/5)
                    templateData = [
                        ['Date', '', 'Account Titles and Explanation', 'Account Number', 'Debit (₱)', 'Credit (₱)'],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', ''],
                        ['', '', '', '', '', '']
                    ];
                    break;
                    
                case 10: // Post-Closing Trial Balance
                    templateData = [
                        ['POST-CLOSING TRIAL BALANCE', '', '', ''],
                        ['December 31, 2024', '', '', ''],
                        ['', '', '', ''],
                        ['Account Title', '', 'Credit', 'Debit'],
                        ['', '', '', ''],
                        ['', '', '', ''],
                        ['', '', '', ''],
                        ['', '', '', ''],
                        ['', '', '', ''],
                        ['', '', '', ''],
                        ['', '', '', ''],
                        ['', '', '', ''],
                        ['', '', '', ''],
                        ['', '', '', '']
                    ];
                    break;
                    
                default:
                    templateData = hot.getData();
            }
            
            const ws = XLSX.utils.aoa_to_sheet(templateData);
            
            // Apply merges and styling for specific steps
            ws['!merges'] = [];
            
            if (step === 1) {
                // Merge header cells for nested headers (row 0)
                ws['!merges'].push(
                    { s: { r: 0, c: 1 }, e: { r: 0, c: 6 } },   // ASSETS
                    { s: { r: 0, c: 7 }, e: { r: 0, c: 8 } },   // LIABILITIES
                    { s: { r: 0, c: 9 }, e: { r: 0, c: 11 } },  // OWNER'S EQUITY
                    { s: { r: 0, c: 12 }, e: { r: 0, c: 15 } }  // EXPENSES
                );
                
                // Set column widths
                ws['!cols'] = Array(16).fill({ wch: 15 });
                
            } else if (step === 2 || step === 5 || step === 8) {
                ws['!merges'].push(
                    { s: { r: 0, c: 0 }, e: { r: 0, c: 1 } }  // Merge Date columns in header
                );
                ws['!cols'] = [
                    { wch: 10 }, // Date month
                    { wch: 10 }, // Date day
                    { wch: 40 }, // Account titles
                    { wch: 15 }, // Account number
                    { wch: 15 }, // Debit
                    { wch: 15 }  // Credit
                ];
                
            } else if (step === 3) {
                const accounts = 20; // Number of accounts
                const colsPerAccount = 6;
                
                // Merge account names (row 0)
                for (let i = 0; i < accounts; i++) {
                    const startCol = i * colsPerAccount;
                    ws['!merges'].push({
                        s: { r: 0, c: startCol },
                        e: { r: 0, c: startCol + colsPerAccount - 1 }
                    });
                }
                
                // Set column widths
                ws['!cols'] = [];
                for (let i = 0; i < accounts; i++) {
                    ws['!cols'].push(
                        { wch: 12 }, // Date
                        { wch: 5 },  // Separator
                        { wch: 15 }, // Debit
                        { wch: 15 }, // Credit
                        { wch: 5 },  // Separator
                        { wch: 12 }  // Date
                    );
                }
                
            } else if (step === 4 || step === 9) {
                ws['!merges'].push(
                    { s: { r: 0, c: 0 }, e: { r: 0, c: 2 } },  // Company name
                    { s: { r: 1, c: 0 }, e: { r: 1, c: 2 } },  // Title
                    { s: { r: 2, c: 0 }, e: { r: 2, c: 2 } }   // Date
                );
                ws['!cols'] = [
                    { wch: 30 }, // Account title
                    { wch: 15 }, // Debit
                    { wch: 15 }  // Credit
                ];
                
            } else if (step === 6) {
                ws['!merges'].push(
                    { s: { r: 0, c: 0 }, e: { r: 0, c: 10 } },  // Company name
                    { s: { r: 1, c: 0 }, e: { r: 1, c: 10 } },  // Title
                    { s: { r: 2, c: 0 }, e: { r: 2, c: 10 } },  // Date
                    { s: { r: 4, c: 1 }, e: { r: 4, c: 2 } },   // Unadjusted TB
                    { s: { r: 4, c: 3 }, e: { r: 4, c: 4 } },   // Adjustments
                    { s: { r: 4, c: 5 }, e: { r: 4, c: 6 } },   // Adjusted TB
                    { s: { r: 4, c: 7 }, e: { r: 4, c: 8 } },   // Income Statement
                    { s: { r: 4, c: 9 }, e: { r: 4, c: 10 } }   // Balance Sheet
                );
                ws['!cols'] = [
                    { wch: 25 }, // Account title
                    ...Array(10).fill({ wch: 12 })
                ];
                
            } else if (step === 7) {
                ws['!merges'].push(
                    // Income Statement headers
                    { s: { r: 0, c: 0 }, e: { r: 0, c: 3 } },
                    { s: { r: 1, c: 0 }, e: { r: 1, c: 3 } },
                    { s: { r: 2, c: 0 }, e: { r: 2, c: 3 } },
                    // Statement of Changes headers
                    { s: { r: 0, c: 4 }, e: { r: 0, c: 7 } },
                    { s: { r: 1, c: 4 }, e: { r: 1, c: 7 } },
                    { s: { r: 2, c: 4 }, e: { r: 2, c: 7 } },
                    // Balance Sheet headers
                    { s: { r: 0, c: 8 }, e: { r: 0, c: 11 } },
                    { s: { r: 1, c: 8 }, e: { r: 1, c: 11 } },
                    { s: { r: 2, c: 8 }, e: { r: 2, c: 11 } }
                );
                ws['!cols'] = [
                    { wch: 25 }, { wch: 12 }, { wch: 12 }, { wch: 5 },   // Income Statement + separator
                    { wch: 25 }, { wch: 12 }, { wch: 12 }, { wch: 5 },   // Changes in Equity + separator
                    { wch: 25 }, { wch: 12 }, { wch: 12 }, { wch: 12 }   // Balance Sheet
                ];
                
            } else if (step === 10) {
                ws['!merges'].push(
                    { s: { r: 0, c: 0 }, e: { r: 0, c: 3 } },  // Title
                    { s: { r: 1, c: 0 }, e: { r: 1, c: 3 } }   // Date
                );
                ws['!cols'] = [
                    { wch: 30 },
                    { wch: 5 },
                    { wch: 15 },
                    { wch: 15 }
                ];
            }
            
            XLSX.utils.book_append_sheet(wb, ws, "Template");
            
            // Generate file name based on step
            const stepNames = {
                1: 'Journal_Entries',
                2: 'Journalizing_Entries',
                3: 'T_Accounts',
                4: 'Trial_Balance',
                5: 'Adjusting_Entries',
                6: 'Worksheet',
                7: 'Financial_Statements',
                8: 'Balance_Sheet',
                9: 'Closing_Entries',
                10: 'Post_Closing_Trial_Balance'
            };
            
            const fileName = `Step_${step}_${stepNames[step] || 'Template'}.xlsx`;
            
            // Download
            XLSX.writeFile(wb, fileName);
        }

        // Success message
        function showSuccessMessage(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'fixed top-4 right-4 z-50 animate-slideDown';
            alertDiv.innerHTML = `
                <div class="flex items-start gap-3 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-lg max-w-md">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-green-800">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
    
    <style>
        /* Enhanced Header Section Styles */
        .answer-key-header {
            background: linear-gradient(135deg, #f9fafb 0%, #f3e8ff 50%, #faf5ff 100%);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e9d5ff;
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.1), 0 2px 4px -1px rgba(139, 92, 246, 0.06);
            position: relative;
            overflow: hidden;
        }

        .answer-key-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(167, 139, 250, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .answer-key-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(196, 181, 253, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3), 0 2px 4px -1px rgba(139, 92, 246, 0.2);
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .step-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(139, 92, 246, 0.4), 0 3px 5px -1px rgba(139, 92, 246, 0.3);
        }

        .step-badge svg {
            width: 1rem;
            height: 1rem;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .header-title {
            font-size: 2.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .header-description {
            color: #6b7280;
            font-size: 1rem;
            line-height: 1.625;
            max-width: 48rem;
            margin-bottom: 0.75rem;
            position: relative;
            z-index: 1;
        }

        .task-info-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.875rem;
            background: white;
            color: #7c3aed;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid #e9d5ff;
            box-shadow: 0 1px 3px 0 rgba(139, 92, 246, 0.1);
            position: relative;
            z-index: 1;
            transition: all 0.2s ease;
        }

        .task-info-badge:hover {
            background: #faf5ff;
            border-color: #d8b4fe;
            transform: translateX(4px);
        }

        .task-info-badge svg {
            width: 1rem;
            height: 1rem;
        }

        /* Instructions Box Enhancement */
        .instructions-box {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            border: 1px solid #e9d5ff;
            border-left: 4px solid #8b5cf6;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(139, 92, 246, 0.08);
        }

        .instructions-box h3 {
            color: #581c87;
            font-size: 0.9375rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .instructions-box p {
            color: #6b21a8;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .instructions-icon {
            width: 1.25rem;
            height: 1.25rem;
            color: #8b5cf6;
            flex-shrink: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .answer-key-header {
                padding: 1.5rem;
            }

            .header-title {
                font-size: 1.75rem;
            }

            .header-description {
                font-size: 0.875rem;
            }

            .step-badge {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }
        }

        @media (min-width: 640px) and (max-width: 1024px) {
            .header-title {
                font-size: 2rem;
            }
        }

        @media (min-width: 1024px) {
            .header-title {
                font-size: 2.5rem;
            }
        }

        body { overflow-x: hidden; }
        .handsontable .font-bold { font-weight: bold; }
        .handsontable .bg-gray-100 { background-color: #f3f4f6 !important; }
        .handsontable .bg-blue-50 { background-color: #eff6ff !important; }
        .handsontable td { border-color: #d1d5db; }
        .handsontable .area { background-color: rgba(147, 51, 234, 0.1); }
        .handsontable { position: relative; z-index: 1; }
        #spreadsheet { isolation: isolate; }
        .overflow-x-auto { -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }

        @media (max-width: 640px) {
            .handsontable { font-size: 12px; }
            .handsontable th, .handsontable td { padding: 4px; }
        }

        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 13px; }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-1rem);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }
    </style>

    

    <div class="py-4 sm:py-6 lg:py-8">
        @if (session('error'))
            <div class="mb-6 animate-slideDown">
                <div class="flex items-start gap-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-red-800 mb-1">Error</h3>
                        <p class="text-sm text-red-700 leading-relaxed">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 animate-slideDown">
                <div class="flex items-start gap-3 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-green-800 mb-1">Success</h3>
                        <p class="text-sm text-green-700 leading-relaxed">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-green-400 hover:text-green-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Enhanced Header Section -->
        <div class="answer-key-header">
            <div class="step-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                <span>Answer Key - Step 1 of 10</span>
            </div>

            <x-spreadsheet-import :step="1" />
            
            <h1 class="header-title">
                Answer Key: Journal Entries
            </h1>
            
            <p class="header-description">
                Create the correct answer key for Journal Entries. This will be used to automatically grade student submissions.
            </p>
            
            <div class="task-info-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>Task: {{ $task->title }}</span>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Instructions Section -->
            <div class="p-4 sm:p-6">
                <div class="instructions-box">
                    <h3>
                        <svg class="instructions-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Instructions for Creating Answer Key
                    </h3>
                    <p>
                        Fill in the correct answers below. Students' submissions will be compared against this answer key for grading. Empty cells will be ignored during comparison.
                    </p>
                </div>
            </div>

            <form id="answerKeyForm" action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 1]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Spreadsheet Section -->
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                        <input type="hidden" name="correct_data" id="correctData" required>
                    </div>

                    <!-- Mobile Scroll Hint -->
                    <div class="mt-2 text-xs text-gray-500 sm:hidden text-center">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Swipe to scroll spreadsheet
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between gap-3 sm:gap-4">
                        <a href="{{ route('instructors.performance-tasks.answer-sheets.show', $task) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Answer Sheets
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Answer Key & Continue
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
    let hot;

    document.addEventListener("DOMContentLoaded", function () {
        const container = document.getElementById('spreadsheet');
        
        // Get saved answer key data if it exists
        const savedData = @json($sheet->correct_data ?? null);
        const initialData = savedData ? JSON.parse(savedData) : Array(15).fill().map(() => Array(15).fill(''));

        // Determine responsive dimensions
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
        
        hot = new Handsontable(container, {
            data: initialData,
            colHeaders: false,
            rowHeaders: true,
            width: '100%',
            height: isMobile ? 350 : (isTablet ? 450 : 500),
            licenseKey: 'non-commercial-and-evaluation',

            nestedHeaders: [
                [
                    '',
                    { label: 'ASSETS', colspan: 6 },
                    { label: 'LIABILITIES', colspan: 2 },
                    { label: "OWNER'S EQUITY", colspan: 3 },
                    { label: 'EXPENSES', colspan: 4 }
                ],
                [
                    '',
                    'Cash', 'Accounts Receivable', 'Supplies', 'Furniture & Fixtures', 'Land', 'Equipment',
                    'Accounts Payable', 'Notes Payable',
                    'Capital', 'Withdrawal', 'Service Revenue',
                    'Rent Expense', 'Utilities Expense', 'Salaries Expense', 'Misc. Expense'
                ]
            ],

            columns: Array(15).fill({ type: 'text' }),
            colWidths: isMobile ? 100 : (isTablet ? 110 : 120),

            // REMOVED HyperFormula integration
            contextMenu: true,
            undo: true,
            manualColumnResize: true,
            manualRowResize: true,
            manualColumnMove: true,
            manualRowMove: true,
            fillHandle: true,
            autoColumnSize: false,
            autoRowSize: false,
            copyPaste: true,
            minRows: 15,
            minCols: 15,
            maxRows: 50, // Prevent infinite recursion
            maxCols: 20, // Prevent infinite recursion
            stretchH: 'none',
            enterMoves: { row: 1, col: 0 },
            tabMoves: { row: 0, col: 1 },
            outsideClickDeselects: false,
            selectionMode: 'multiple',
            mergeCells: true,
            comments: true,
            customBorders: true,

            // Simple formula handling without HyperFormula
            cells: function(row, col) {
                const cellProperties = {};
                
                // Custom renderer for all cells to handle basic formulas
                cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                    // Handle basic formulas that start with =
                    if (value && typeof value === 'string' && value.startsWith('=')) {
                        try {
                            const result = evaluateSimpleFormula(value, instance, row, col);
                            if (result !== null) {
                                // Display the calculated result
                                Handsontable.renderers.TextRenderer.call(
                                    this, instance, td, row, col, prop, result, cellProperties
                                );
                                td.style.color = '#1a73e8';
                                td.style.fontWeight = '500';
                                td.title = `Formula: ${value}`;
                                td.classList.add('formula-cell');
                                return;
                            } else {
                                // If we can't evaluate it, show the formula as text
                                Handsontable.renderers.TextRenderer.call(
                                    this, instance, td, row, col, prop, value, cellProperties
                                );
                                td.style.color = '#666';
                                td.title = 'Formula (unsupported)';
                                return;
                            }
                        } catch (error) {
                            // Show formula with error styling
                            Handsontable.renderers.TextRenderer.call(
                                this, instance, td, row, col, prop, value, cellProperties
                            );
                            td.style.color = '#ff4444';
                            td.style.fontStyle = 'italic';
                            td.title = 'Formula error';
                            return;
                        }
                    }
                    
                    // Default rendering for non-formula cells
                    Handsontable.renderers.TextRenderer.call(
                        this, instance, td, row, col, prop, value, cellProperties
                    );
                };
                
                return cellProperties;
            },

            // Clean up formula input
            beforeChange: function(changes, source) {
                if (source === 'edit' && changes) {
                    changes.forEach(function(change) {
                        const newValue = change[3];
                        if (newValue && typeof newValue === 'string' && newValue.startsWith('=')) {
                            change[3] = newValue.trim();
                        }
                    });
                }
                return true;
            }
        });

        // Simple formula evaluation function
        function evaluateSimpleFormula(formula, instance, currentRow, currentCol) {
            if (!formula.startsWith('=')) return null;
            
            const expression = formula.substring(1).trim();
            
            try {
                // 1. Handle basic arithmetic with numbers only
                if (/^[\d\s\+\-\*\/\(\)\.]+$/.test(expression)) {
                    // Safe evaluation - only numbers and operators
                    const result = Function(`"use strict"; return (${expression})`)();
                    return typeof result === 'number' ? result : null;
                }
                
                // 2. Handle simple cell references (A1, B2 format)
                const cellRefMatch = expression.match(/^([A-Z]+)(\d+)$/);
                if (cellRefMatch) {
                    const [, colLetters, rowNum] = cellRefMatch;
                    const colIndex = columnLetterToIndex(colLetters);
                    const rowIndex = parseInt(rowNum) - 1;
                    
                    if (rowIndex >= 0 && colIndex >= 0 && rowIndex < instance.countRows() && colIndex < instance.countCols()) {
                        const cellValue = instance.getDataAtCell(rowIndex, colIndex);
                        const numValue = parseFloat(cellValue);
                        return isNaN(numValue) ? 0 : numValue;
                    }
                    return 0;
                }
                
                // 3. Handle simple arithmetic with cell references (A1+B1)
                const arithmeticMatch = expression.match(/^([A-Z]+\d+)\s*([\+\-\*\/])\s*([A-Z]+\d+)$/);
                if (arithmeticMatch) {
                    const [, ref1, operator, ref2] = arithmeticMatch;
                    
                    const val1 = getCellValueByReference(ref1, instance);
                    const val2 = getCellValueByReference(ref2, instance);
                    
                    switch (operator) {
                        case '+': return val1 + val2;
                        case '-': return val1 - val2;
                        case '*': return val1 * val2;
                        case '/': return val2 !== 0 ? val1 / val2 : '#DIV/0!';
                    }
                }
                
                // 4. Handle simple SUM function
                const sumMatch = expression.match(/^SUM\(([A-Z]+\d+):([A-Z]+\d+)\)$/i);
                if (sumMatch) {
                    const [, startRef, endRef] = sumMatch;
                    return sumRange(startRef, endRef, instance);
                }
                
                return null; // Unsupported formula type
                
            } catch (error) {
                console.warn('Formula evaluation error:', error, 'Formula:', formula);
                return '#ERROR!';
            }
        }

        // Helper function to get cell value by reference (A1, B2, etc.)
        function getCellValueByReference(cellRef, instance) {
            const match = cellRef.match(/^([A-Z]+)(\d+)$/);
            if (!match) return 0;
            
            const [, colLetters, rowNum] = match;
            const colIndex = columnLetterToIndex(colLetters);
            const rowIndex = parseInt(rowNum) - 1;
            
            if (rowIndex >= 0 && colIndex >= 0 && rowIndex < instance.countRows() && colIndex < instance.countCols()) {
                const cellValue = instance.getDataAtCell(rowIndex, colIndex);
                return parseFloat(cellValue) || 0;
            }
            return 0;
        }

        // Helper function to sum a range
        function sumRange(startRef, endRef, instance) {
            const startMatch = startRef.match(/^([A-Z]+)(\d+)$/);
            const endMatch = endRef.match(/^([A-Z]+)(\d+)$/);
            
            if (!startMatch || !endMatch) return 0;
            
            const [, startColLetters, startRowNum] = startMatch;
            const [, endColLetters, endRowNum] = endMatch;
            
            const startCol = columnLetterToIndex(startColLetters);
            const startRow = parseInt(startRowNum) - 1;
            const endCol = columnLetterToIndex(endColLetters);
            const endRow = parseInt(endRowNum) - 1;
            
            let sum = 0;
            for (let row = startRow; row <= endRow; row++) {
                for (let col = startCol; col <= endCol; col++) {
                    if (row >= 0 && col >= 0 && row < instance.countRows() && col < instance.countCols()) {
                        const cellValue = instance.getDataAtCell(row, col);
                        sum += parseFloat(cellValue) || 0;
                    }
                }
            }
            return sum;
        }

        // Convert column letters to index (A=0, B=1, ..., Z=25, AA=26, etc.)
        function columnLetterToIndex(letters) {
            let result = 0;
            for (let i = 0; i < letters.length; i++) {
                result *= 26;
                result += letters.charCodeAt(i) - 'A'.charCodeAt(0);
            }
            return result;
        }

        // Responsive resize handler
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const newIsMobile = window.innerWidth < 640;
                const newIsTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
                const newHeight = newIsMobile ? 350 : (newIsTablet ? 450 : 500);
                
                hot.updateSettings({
                    height: newHeight,
                    colWidths: newIsMobile ? 100 : (newIsTablet ? 110 : 120)
                });
            }, 250);
        });

        // Capture spreadsheet data on submit
        const answerKeyForm = document.getElementById("answerKeyForm");
        if (answerKeyForm) {
            answerKeyForm.addEventListener("submit", function (e) {
                e.preventDefault();
                const data = hot.getData();
                document.getElementById("correctData").value = JSON.stringify(data);
                this.submit();
            });
        }

        // Add CSS for formula cells
        const style = document.createElement('style');
        style.textContent = `
            .formula-cell {
                background-color: #f8f9fa !important;
            }
        `;
        document.head.appendChild(style);

        console.log('Spreadsheet initialized without HyperFormula - basic formulas supported');
    });
</script>
</x-app-layout>