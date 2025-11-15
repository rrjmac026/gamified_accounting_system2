@props(['data' => null, 'step' => 1])

<div class="answer-sheet-display-{{ $step }}"></div>

<script>
    (function() {
        const container = document.querySelector('.answer-sheet-display-{{ $step }}');
        const rawData = @json($data);
        const stepNumber = parseInt('{{ $step }}'.replace(/[^0-9]/g, ''));
        
        console.log('Display component - Step:', stepNumber, 'Raw data:', rawData);
        
        // Parse data - handle double encoding and {data, metadata} structure
        let displayData = null;
        let metadata = null;
        
        if (rawData) {
            try {
                let parsed = rawData;
                // Handle multiple levels of JSON encoding
                while (typeof parsed === 'string') {
                    parsed = JSON.parse(parsed);
                }
                
                // Check if it has {data, metadata} structure
                if (parsed && typeof parsed === 'object' && parsed.data) {
                    displayData = parsed.data;
                    metadata = parsed.metadata;
                } else if (Array.isArray(parsed)) {
                    displayData = parsed;
                }
            } catch (e) {
                console.error('Error parsing data:', e);
                displayData = rawData;
            }
        }
        
        if (!displayData || !Array.isArray(displayData)) {
            container.innerHTML = '<div class="p-4 text-center text-gray-500">No data available</div>';
            return;
        }
        
        console.log('Parsed displayData:', displayData);
        
        // Step 4 specific rendering (Trial Balance)
        if (stepNumber === 4) {
            const hyperformulaInstance = HyperFormula.buildEmpty({
                licenseKey: 'internal-use-in-handsontable',
                ignoreWhiteSpace: 'any',
            });

            function pesoRenderer(instance, td, row, col, prop, value, cellProperties) {
                Handsontable.renderers.NumericRenderer.apply(this, arguments);
                
                if (value !== null && value !== undefined && value !== '') {
                    const numValue = typeof value === 'number' ? value : parseFloat(String(value).replace(/[,₱\s]/g, ''));
                    if (!isNaN(numValue)) {
                        td.innerHTML = '₱' + numValue.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
                
                return td;
            }

            new Handsontable(container, {
                data: displayData,
                columns: [
                    { type: 'text' },
                    { type: 'numeric', renderer: pesoRenderer },
                    { type: 'numeric', renderer: pesoRenderer },
                ],
                rowHeaders: true,
                licenseKey: 'non-commercial-and-evaluation',
                height: 450,
                stretchH: 'all',
                readOnly: true,
                formulas: { engine: hyperformulaInstance },
                className: 'htCenter htMiddle',
                
                cells: function(row, col) {
                    const cellProperties = {};
                    
                    // Row 0: Company name
                    if (row === 0) {
                        if (col === 0 || col === 2) {
                            cellProperties.renderer = function(instance, td) {
                                td.innerHTML = '';
                                td.style.background = 'white';
                                td.style.border = 'none';
                            };
                        } else if (col === 1) {
                            cellProperties.renderer = function(instance, td, row, col, prop, value) {
                                Handsontable.renderers.TextRenderer.apply(this, arguments);
                                td.innerHTML = '<strong>' + (value || 'Durano Enterprise') + '</strong>';
                                td.style.textAlign = 'center';
                                td.style.fontSize = '16px';
                            };
                        }
                    }
                    // Row 1: Title
                    else if (row === 1) {
                        if (col === 0 || col === 2) {
                            cellProperties.renderer = function(instance, td) {
                                td.innerHTML = '';
                                td.style.background = 'white';
                                td.style.border = 'none';
                            };
                        } else if (col === 1) {
                            cellProperties.renderer = function(instance, td, row, col, prop, value) {
                                Handsontable.renderers.TextRenderer.apply(this, arguments);
                                td.innerHTML = '<strong>' + (value || 'Trial Balance') + '</strong>';
                                td.style.textAlign = 'center';
                                td.style.fontSize = '14px';
                            };
                        }
                    }
                    // Row 2: Date
                    else if (row === 2) {
                        if (col === 0 || col === 2) {
                            cellProperties.renderer = function(instance, td) {
                                td.innerHTML = '';
                                td.style.background = 'white';
                                td.style.border = 'none';
                            };
                        } else if (col === 1) {
                            cellProperties.renderer = function(instance, td, row, col, prop, value) {
                                Handsontable.renderers.TextRenderer.apply(this, arguments);
                                td.innerHTML = '<strong>' + (value || 'Date: ____________') + '</strong>';
                                td.style.textAlign = 'center';
                            };
                        }
                    }
                    // Row 3: Column headers
                    else if (row === 3) {
                        cellProperties.renderer = function(instance, td, row, col, prop, value) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>' + value + '</strong>';
                            td.style.textAlign = 'center';
                            td.style.backgroundColor = '#f3f4f6';
                            td.style.fontWeight = 'bold';
                        };
                    }
                    // Data rows
                    else {
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            if (col === 0) {
                                Handsontable.renderers.TextRenderer.apply(this, arguments);
                            } else {
                                pesoRenderer.apply(this, arguments);
                            }
                            
                            // Check metadata for bold
                            if (metadata && metadata[row] && metadata[row][col] && metadata[row][col].bold) {
                                td.style.fontWeight = 'bold';
                            }
                        };
                    }
                    
                    return cellProperties;
                }
            });
        } else {
            // Generic rendering for other steps
            new Handsontable(container, {
                data: displayData,
                readOnly: true,
                rowHeaders: true,
                colHeaders: true,
                height: 400,
                licenseKey: 'non-commercial-and-evaluation',
                stretchH: 'all'
            });
        }
    })();
</script>

<style>
    .handsontable td { 
        border-color: #d1d5db;
        background-color: #ffffff;
    }
</style>