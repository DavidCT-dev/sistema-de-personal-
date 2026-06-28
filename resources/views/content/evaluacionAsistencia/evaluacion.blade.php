@extends('layouts.master')

@section('script')
{{-- <script src="{{ asset('lang/flatpickr.js') }}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.full.min.js"></script>

<script>
    // Configura las fuentes disponibles (Roboto ya está incluida en vfs_fonts.js)
    pdfMake.fonts = {
        Roboto: {
            normal: 'Roboto-Regular.ttf',
            bold: 'Roboto-Medium.ttf',
            italics: 'Roboto-Italic.ttf',
            bolditalics: 'Roboto-MediumItalic.ttf'
        }
    };
    // Establece Roboto como fuente predeterminada
    pdfMake.defaultFonts = {
        default: 'Roboto'
    };
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generateExcelBtn = document.getElementById('generateExcelBtn');
        if (!generateExcelBtn) {
            console.error('El botón generateExcelBtn no fue encontrado');
            return;
        }
        generateExcelBtn.addEventListener('click', async function() {
            const fechaInicio = document.getElementById('fechaInicioReporte').value;
            const fechaFin = document.getElementById('fechaFinReporte').value;
            const tipoContrato = document.getElementById('tipoContrato').value;
            if (!fechaInicio || !fechaFin || !tipoContrato) {
                alert('Todos los campos son requeridos');
                return;
            }
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i> Generando...';
            btn.disabled = true;
            try {
                const response = await fetch("{{ route('pdf-generate-asistencia-todos') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        fechaInicioReporte: fechaInicio,
                        fechaFinReporte: fechaFin,
                        tipoContrato: tipoContrato
                    })
                });
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                if (!data.reportes) throw new Error('Datos incompletos recibidos del servidor');
                const titulo = `Reporte de Asistencia del ${fechaInicio} al ${fechaFin}`;
                await generateExcel(data.reportes, titulo);
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Ocurrió un error al generar el Excel');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
        async function generateExcel(reportes, titulo) {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Reporte Asistencia');
            worksheet.mergeCells('A1:N1');
            const titleRow = worksheet.getRow(1);
            titleRow.getCell(1).value = titulo;
            worksheet.addRow([]);
            const headers = [
                'N°', 'ITEM', 'CI', 'PERSONAL', 'CARGO', 'DIAS_TE', 'CUENTA_HABER', 'CUENTA_VACACION',
                'VACACION', 'BAJA_MEDICA', 'FALTA', 'ABANDONO', 'ATRASOS',
                'OTRO MOTIVO Y COMISIO VEATICOS'
            ];
            worksheet.addRow(headers);
            reportes.forEach((reporte, index) => {
                const totals = calculateTotals(reporte);
                const detalle = reporte.detalle_permisos || {};
                const cuentaHaber = detalle.cuenta_haber || 0;
                const cuentaVacacion = detalle.cuenta_vacacion || 0;
                const vacacion = detalle.vacacion || 0;
                const bajaMedica = detalle.baja_medica || 0;
                const otros = detalle.otros || 0;
                const fechasFalta = new Set((detalle.faltas || []).map(f => f.fecha));
                const fechasAbandono = new Set((detalle.abandono || []).map(a => a.fecha));
                const fechasPenalizadas = new Set([...fechasFalta, ...fechasAbandono]);
                const penalizacionFaltasAbandono = [...fechasPenalizadas].length * 0.5;
                worksheet.addRow([
                    (index + 1).toString().padStart(4, '0'),
                    reporte.empleado.item,
                    reporte.empleado.ci,
                    `${reporte.empleado.apellido_pat} ${reporte.empleado.apellido_mat} ${reporte.empleado.nombres}`,
                    reporte.empleado.cargo.descripcion,
                    totals.dias,
                    cuentaHaber,
                    cuentaVacacion,
                    vacacion,
                    bajaMedica,
                    totals.faltas,
                    totals.abandono,
                    totals.atrasos,
                    otros,
                ]);
            });
            applyExcelStyles(worksheet, reportes.length + 3, headers.length);
            worksheet.getColumn(1).width = 5; // N°
            worksheet.getColumn(2).width = 5; // ITEM
            worksheet.getColumn(3).width = 8; // CI
            worksheet.getColumn(4).width = 25; // PERSONAL
            worksheet.getColumn(5).width = 20; // CARGO
            worksheet.getColumn(8).width = 10; // DIAS_TE
            worksheet.getColumn(9).width = 12; // CUENTA_HABER
            worksheet.getColumn(10).width = 14; // CUENTA_VACACION
            worksheet.getColumn(11).width = 10; // VACACION
            worksheet.getColumn(12).width = 14; // BAJA_MEDICA
            worksheet.getColumn(13).width = 8; // FALTA
            worksheet.getColumn(14).width = 10; // ABANDONO
            worksheet.getColumn(15).width = 10; // ATRASOS
            worksheet.getColumn(16).width = 25; // OTRO MOTIVO Y COMISIO VEATICOS
            const buffer = await workbook.xlsx.writeBuffer();
            saveAs(new Blob([buffer]), `Reporte_Asistencia_${titulo.replace(/ /g, '_')}.xlsx`);
        }

        function calculateTotals(reporte) {
            return reporte.marcacionesFinales.reduce((acc, marcacion) => {
                const [h, m, s] = (marcacion.horas_trabajo || "00:00:00").split(':').map(Number);
                const minutosTrabajados = (h * 60) + m + (s / 60);
                const horasNormales = 480;
                const minutosExtra = Math.max(0, minutosTrabajados - horasNormales);
                const horasExtra = Math.floor(minutosExtra / 60);
                const minsExtra = Math.floor(minutosExtra % 60);
                const horasExtraFormato =
                    `${horasExtra.toString().padStart(2, '0')}:${minsExtra.toString().padStart(2, '0')}:00`;
                return {
                    atrasos: acc.atrasos + (marcacion.atrasos || 0),
                    abandono: acc.abandono + (marcacion.abandono || 0),
                    faltas: acc.faltas + (marcacion.faltas || 0),
                    dias: acc.dias + (marcacion.dias_trabajo || 0),
                    horas_trabajo: sumarHoras(acc.horas_trabajo || "00:00:00", marcacion
                        .horas_trabajo || "00:00:00"),
                };
            }, {
                atrasos: 0,
                abandono: 0,
                faltas: 0,
                dias: 0,
                horas_trabajo: "00:00:00",
            });
        }

        function sumarHoras(h1, h2) {
            const [h1h, h1m, h1s] = h1.split(':').map(Number);
            const [h2h, h2m, h2s] = h2.split(':').map(Number);
            let segundos = h1s + h2s;
            let minutos = h1m + h2m + Math.floor(segundos / 60);
            let horas = h1h + h2h + Math.floor(minutos / 60);
            segundos %= 60;
            minutos %= 60;
            return `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
        }

        function applyExcelStyles(worksheet, rowCount, colCount) {
            worksheet.eachRow(row => {
                row.eachCell(cell => {
                    cell.font = {
                        name: 'Arial Narrow',
                        size: 10
                    };
                    cell.alignment = {
                        vertical: 'middle',
                        horizontal: 'center',
                        wrapText: true
                    };
                    cell.border = {
                        top: {
                            style: 'thin',
                            color: {
                                argb: 'FF000000'
                            }
                        },
                        left: {
                            style: 'thin',
                            color: {
                                argb: 'FF000000'
                            }
                        },
                        bottom: {
                            style: 'thin',
                            color: {
                                argb: 'FF000000'
                            }
                        },
                        right: {
                            style: 'thin',
                            color: {
                                argb: 'FF000000'
                            }
                        }
                    };
                });
            });
            const titleCell = worksheet.getRow(1).getCell(1);
            titleCell.font = {
                name: 'Arial Narrow',
                size: 15,
                bold: true
            };
            titleCell.alignment = {
                vertical: 'middle',
                horizontal: 'center'
            };
            titleCell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: {
                    argb: 'FFD2B48C'
                }
            };
            const headerRow = worksheet.getRow(3);
            headerRow.eachCell(cell => {
                cell.font = {
                    name: 'Arial Narrow',
                    size: 10,
                    bold: true
                };
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: {
                        argb: 'FFD2B48C'
                    }
                };
            });
            const columnStyles = {
                6: {
                    argb: 'FF006400'
                },
                9: {
                    argb: 'FFDEEAF6'
                },
                10: {
                    argb: 'FFDEEAF6'
                },
                11: {
                    argb: 'FFDEEAF6'
                },
                12: {
                    argb: 'FFDEEAF6'
                },
                13: {
                    argb: 'FFDEEAF6'
                },
                14: {
                    argb: 'FFDEEAF6'
                }
            };
            for (let i = 4; i <= rowCount; i++) {
                const row = worksheet.getRow(i);
                Object.keys(columnStyles).forEach(colNum => {
                    const cell = row.getCell(parseInt(colNum));
                    if (cell) {
                        cell.fill = {
                            type: 'pattern',
                            pattern: 'solid',
                            fgColor: columnStyles[colNum]
                        };
                    }
                });
            }
        }
    });
</script>

<script>
    // generateExcelBtn
    document.addEventListener('DOMContentLoaded', function() {
        const generatePdfBtn = document.getElementById('generatePdfBtn');
        if (!generatePdfBtn) {
            console.error('El botón generatePdfBtn no fue encontrado');
            return;
        }
        generatePdfBtn.addEventListener('click', async function() {
            const fechaInicio = document.getElementById('fechaInicioReporte').value;
            const fechaFin = document.getElementById('fechaFinReporte').value;
            const tipoContrato = document.getElementById('tipoContrato').value;
            if (!fechaInicio || !fechaFin || !tipoContrato) {
                alert('Todos los campos son requeridos');
                return;
            }
            // Mostrar loading
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i> Generando...';
            btn.disabled = true;
            try {
                // Enviar datos al servidor
                const response = await fetch("{{ route('pdf-generate-asistencia-todos') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        fechaInicioReporte: fechaInicio,
                        fechaFinReporte: fechaFin,
                        tipoContrato: tipoContrato
                    })
                });
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                if (!data.reportes || !data.images) {
                    throw new Error('Datos incompletos recibidos del servidor');
                }
                const autfLogo = data.images.autfLogo;
                const personalLogo = data.images.personalLogo;
                // Generar el documento PDF
                const pdfDoc = generatePdfMakeDocument(data.reportes, fechaInicio, fechaFin,
                    autfLogo, personalLogo);
                // Abrir PDF en nueva ventana
                pdfMake.createPdf(pdfDoc).open({}, window.open('', '_blank'));
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Ocurrió un error al generar el PDF');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    });
    // Función para generar el documento con pdfmake
    function generatePdfMakeDocument(reportes, fechaInicio, fechaFin, autfLogo, personalLogo) {
        // Definir estilos
        const styles = {
            header: {
                fontSize: 14,
                // bold: true,
                alignment: 'center',
                margin: [0, 0, 0, 10],
                // font: 'Times'
            },
            subheader: {
                fontSize: 12,
                // bold: true,
                margin: [0, 10, 0, 5],
                // font: 'Times'
            },
            tableHeader: {
                // bold: true,
                fontSize: 8,
                color: 'black',
                fillColor: '#f2f2f2',
                // font: 'Times'
            },
            tableRow: {
                fontSize: 7,
                // font: 'Times'
            },
            badge: {
                fontSize: 8,
                // bold: true,
                color: 'white',
                margin: [2, 2, 2, 2],
                // font: 'Times'
            },
            employeeInfo: {
                fontSize: 10,
                //font: 'Times'
            }
        };
        // Crear contenido para cada empleado
        const employeeContents = reportes.map(reporte => {
            let totalAtraso = 0;
            let totalAbandono = 0;
            let totalFaltas = 0;
            let totalDiasTrabajo = 0;
            const marcacionesValidas = reporte.marcacionesFinales.filter(m => m.dia);
            // Calcular totales
            marcacionesValidas.forEach(marcacion => {
                totalAtraso += marcacion.atrasos || 0;
                totalAbandono += marcacion.abandono || 0;
                totalFaltas += marcacion.faltas || 0;
                totalDiasTrabajo += marcacion.dias_trabajo || 0;
            });
            // Crear filas de la tabla
            const tableRows = marcacionesValidas.map(marcacion => {
                const row = [{
                        text: marcacion.fecha,
                        style: 'tableRow',
                    },
                    {
                        text: marcacion.dia,
                        style: 'tableRow',
                    }
                ];
                row.push({
                    text: marcacion.entrada_1 || '00:00:00',
                    style: 'tableRow',
                }, {
                    text: marcacion.salida_1 || '00:00:00',
                    style: 'tableRow',
                }, {
                    text: marcacion.entrada_2 || '00:00:00',
                    style: 'tableRow',
                }, {
                    text: marcacion.salida_2 || '00:00:00',
                    style: 'tableRow',
                });
                row.push({
                    text: marcacion.horas_trabajo || '0.0',
                    style: 'tableRow',
                }, {
                    text: marcacion.atrasos ? Number(marcacion.atrasos).toFixed(1) : '0.0',
                    style: 'tableRow',
                }, {
                    text: marcacion.abandono ? Number(marcacion.abandono).toFixed(1) : '0.0',
                    style: 'tableRow',
                }, {
                    text: marcacion.faltas ? Number(marcacion.faltas).toFixed(1) : '0.0',
                    style: 'tableRow',
                }, {
                    text: marcacion.dias_trabajo ? Number(marcacion.dias_trabajo).toFixed(1) :
                        '0.0',
                    style: 'tableRow',
                }, {
                    text: (marcacion.observaciones || '').toLowerCase(),
                    style: 'tableRow',
                });
                return row;
            });
            // Añadir fila de totales
            tableRows.push([{
                    text: 'Totales',
                    colSpan: 7,
                    style: 'tableRow',
                    bold: true
                },
                {}, {}, {}, {}, {}, {},
                {
                    text: totalAtraso.toFixed(1),
                    style: 'tableRow'
                },
                {
                    text: totalAbandono.toFixed(1),
                    style: 'tableRow'
                },
                {
                    text: totalFaltas.toFixed(1),
                    style: 'tableRow'
                },
                {
                    text: totalDiasTrabajo.toFixed(1),
                    style: 'tableRow'
                },
                {
                    text: '',
                    style: 'tableRow'
                }
            ]);
            // Crear contenido para el empleado
            return [
                // Encabezado con imágenes
                {
                    table: {
                        widths: ['auto', '*', 'auto'],
                        body: [
                            [{
                                    image: 'autfLogo',
                                    width: 60,
                                    height: 60,
                                    margin: [0, 0, 0, 0]
                                },
                                {
                                    stack: [{
                                            text: 'UNIVERSIDAD AUTÓNOMA TOMÁS FRÍAS',
                                            style: ['header', {
                                                fontSize: 12,
                                                margin: [0, 10, 0, 0]
                                            }]
                                        },
                                        {
                                            text: 'DEPARTAMENTO DE PERSONAL',
                                            style: ['header', {
                                                fontSize: 12,
                                                margin: [0, 0, 0, 0]
                                            }]
                                        }
                                    ],
                                    margin: [0, 0, 0, 0]
                                },
                                {
                                    image: 'personalLogo',
                                    width: 60,
                                    height: 60,
                                    alignment: 'right',
                                    margin: [0, 0, 0, 0]
                                }
                            ]
                        ]
                    },
                    layout: 'noBorders',
                    margin: [0, 0, 0, 10]
                },
                {
                    text: 'REPORTE GENERAL DE ASISTENCIA',
                    style: 'header',
                    margin: [0, 0, 0, 5]
                },
                {
                    text: `Desde: ${fechaInicio}   Hasta: ${fechaFin}`,
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                },
                // Información del empleado
                {
                    table: {
                        widths: ['*', '*'],
                        body: [
                            [{
                                    text: [{
                                            text: 'Id Empleado: ',
                                            bold: true,
                                            style: 'employeeInfo'
                                        },
                                        {
                                            text: reporte.empleado.ci + '\n',
                                            style: 'employeeInfo'
                                        },
                                        {
                                            text: 'Apellido y Nombres: ',
                                            bold: true,
                                            style: 'employeeInfo'
                                        },
                                        {
                                            text: `${reporte.empleado.apellido_pat} ${reporte.empleado.apellido_mat} ${reporte.empleado.nombres}\n`,
                                            style: 'employeeInfo'
                                        },
                                        {
                                            text: 'Cargo: ',
                                            bold: true,
                                            style: 'employeeInfo'
                                        },
                                        {
                                            text: reporte.empleado.cargo.descripcion,
                                            style: 'employeeInfo'
                                        }
                                    ],
                                    style: 'employeeInfo'
                                },
                                {
                                    text: [
                                        ...reporte.empleado.horarios.flatMap(horario => [{
                                                text: 'Horario: ',
                                                bold: true,
                                                style: 'employeeInfo'
                                            },
                                            {
                                                text: horario.descripcion + '\n',
                                                style: 'employeeInfo'
                                            },
                                        ]),
                                        {
                                            text: 'Tipo Contrato: ',
                                            bold: true,
                                            style: 'employeeInfo'
                                        },
                                        {
                                            text: reporte.empleado.tipoContrato.descripcion + '\n',
                                            style: 'employeeInfo'
                                        },
                                        {
                                            text: 'Lugar Trabajo: ',
                                            bold: true,
                                            style: 'employeeInfo'
                                        },
                                        {
                                            text: reporte.empleado.lugarTrabajo.descripcion ||
                                                'N/A',
                                            style: 'employeeInfo'
                                        }
                                    ],
                                    alignment: 'right',
                                    style: 'employeeInfo'
                                }
                            ]
                        ]
                    },
                    layout: 'noBorders',
                    margin: [0, 0, 0, 10]
                },
                // Tabla de marcaciones
                {
                    table: {
                        headerRows: 1,
                        widths: ['auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto',
                            'auto', 'auto', 100
                        ],
                        body: [
                            [{
                                    text: 'Fecha',
                                    style: 'tableHeader',
                                },
                                {
                                    text: 'Dia',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'ingreso1',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'salida1',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'ingreso2',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'salida2',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Horas Trabajo',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Atrasos',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Abandono',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Faltas',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Dias Trabajo',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Observaciones',
                                    style: 'tableHeader'
                                }
                            ],
                            ...tableRows
                        ]
                    },
                    layout: {
                        hLineWidth: function(i, node) {
                            return 0.5;
                        },
                        vLineWidth: function(i, node) {
                            return 0.5;
                        },
                        hLineColor: function(i, node) {
                            return 'black';
                        },
                        vLineColor: function(i, node) {
                            return 'black';
                        },
                        fillColor: function(rowIndex, node, columnIndex) {
                            return (rowIndex === 0) ? '#f2f2f2' : null;
                        }
                    },
                    margin: [0, 0, 0, 10]
                },
                // Totales
                {
                    table: {
                        widths: ['50%', '50%'],
                        body: [
                            [{
                                    text: [{
                                            text: 'Días Bono Té: ',
                                            bold: true
                                        },
                                        {
                                            text: ` ${totalDiasTrabajo} `
                                        },
                                        '\n',
                                        {
                                            text: 'Abandonos: ',
                                            bold: true
                                        },
                                        {
                                            text: ` ${totalAbandono} `
                                        },
                                        '\n',
                                        {
                                            text: 'Faltas: ',
                                            bold: true
                                        },
                                        {
                                            text: ` ${totalFaltas} `
                                        },
                                        '\n',
                                        {
                                            text: 'Atrasos: ',
                                            bold: true
                                        },
                                        {
                                            text: ` ${totalAtraso} `
                                        }
                                    ],
                                    margin: [0, 5, 0, 0]
                                },
                                {
                                    text: [{
                                            text: 'Cuenta Haber: ',
                                            bold: true
                                        },
                                        {
                                            text: ` ${reporte.empleado.total_dias_vacacion || 0} `
                                        },
                                        '\n',
                                        {
                                            text: 'Vacaciones: ',
                                            bold: true
                                        },
                                        {
                                            text: ` ${reporte.vacaciones || 0} `
                                        },
                                        '\n',
                                        {
                                            text: 'Permisos: ',
                                            bold: true
                                        },
                                        {
                                            text: ` ${reporte.permisos || 0} `
                                        }
                                    ],
                                    margin: [0, 5, 0, 0],
                                    alignment: 'right'
                                }
                            ]
                        ]
                    },
                    layout: 'noBorders',
                    margin: [0, 10, 0, 20]
                },
                {
                    text: '',
                    pageBreak: 'after'
                }
            ];
        });
        // Concatenar todos los contenidos de empleados
        const content = [].concat(...employeeContents);
        // Quitar el último pageBreak
        if (content.length > 0 && content[content.length - 1].pageBreak) {
            content.pop();
        }
        // autfLogo, personalLogo
        // Definir el documento final
        return {
            content: content,
            styles: styles,
            images: {
                autfLogo: autfLogo, // Reemplaza con tu imagen en base64
                personalLogo: personalLogo // Reemplaza con tu imagen en base64
            },
            pageSize: 'A4',
            pageMargins: [20, 20, 20, 20]
        };
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Configuración de localización en español
        flatpickr.localize(flatpickr.l10ns.es);
        // Inicializar el Timepicker en los campos seleccionados
        flatpickr("#fechaInicioReporte, #fechaFinReporte", {
            enableTime: false, // Deshabilitar la selección de tiempo
            dateFormat: "d-m-Y", // Formato de fecha
            time_24hr: true, // Usar formato de 24 horas
            weekNumbers: true, // Mostrar números de la semana
            allowInput: true, // Habilita la escritura manual
        });
    });
</script>

<script>
    document.getElementById('editButton').addEventListener('click', function() {
        // Habilitar todos los campos editables
        const editableFields = document.querySelectorAll('.editable-field');
        editableFields.forEach(field => {
            field.removeAttribute('readonly');
            field.classList.remove('bg-slate-100', 'dark:bg-zink-600');
            field.classList.add('bg-white', 'dark:bg-zink-700');
        });
        // Cambiar visibilidad de botones
        this.classList.add('hidden');
        document.getElementById('saveButton').classList.remove('hidden');
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Actualizar el action del formulario con el ID del empleado seleccionado
        const form = document.getElementById('formAsistencia');
        const empleadoSelect = document.getElementById('empleadoId');
        empleadoSelect.addEventListener('change', function() {
            const route = form.getAttribute('action');
            form.setAttribute('action', route.replace(':id', this.value));
        });
        // Si ya hay un empleado seleccionado, actualizar el action del formulario
        if (empleadoSelect.value) {
            const route = form.getAttribute('action');
            form.setAttribute('action', route.replace(':id', empleadoSelect.value));
        }
    });
</script>
@endsection

@section('content')

<div
    class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Asistencia Empleado</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#" class="text-slate-400 dark:text-zink-200">Evaluación - Asistencia</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Evaluacion
                </li>
            </ul>

        </div>

        @can('evaluacion_reporte_todos')
        <form action="{{ route('pdf-generate-asistencia-todos') }}" method="POST" target="_blank"
            class="p-4 bg-slate-50 dark:bg-zink-700 rounded-md shadow mb-3">
            @csrf

            <div class="flex flex-col md:flex-row gap-4 items-end">
                <!-- Fecha inicio -->
                <div class="flex-1 min-w-[200px]">
                    <label for="fechaInicioReporte"
                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Fecha inicio <span class="text-red-500">*</span>
                    </label>
                    <input type="text" required id="fechaInicioReporte" name="fechaInicioReporte"
                        class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                                disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                                dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                                placeholder:text-slate-400 dark:placeholder:text-zink-200 p-2 rounded-md" data-provider="flatpickr" data-date-format="d-m-Y" readonly="readonly"
                        placeholder="DD-MM-YYYY">
                </div>

                <!-- Fecha fin -->
                <div class="flex-1 min-w-[200px]">
                    <label for="fechaFinReporte"
                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Fecha fin <span class="text-red-500">*</span>
                    </label>
                    <input type="text" required id="fechaFinReporte" name="fechaFinReporte"
                        class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                                disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                                dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                                placeholder:text-slate-400 dark:placeholder:text-zink-200 p-2 rounded-md" data-provider="flatpickr" data-date-format="d-m-Y"
                        readonly="readonly" placeholder="DD-MM-YYYY">
                </div>

                <!-- Tipo de Contrato -->
                <div class="flex-1 min-w-[200px]">
                    <label for="tipoContrato" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tipo Contrato <span class="text-red-500">*</span>
                    </label>
                    <select id="tipoContrato" name="tipoContrato" required class="form-input w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                                            disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                                            dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                                            placeholder:text-slate-400 dark:placeholder:text-zink-200 p-2 rounded-md">
                        <option value="" selected disabled>Seleccione Contrato</option>
                        <option value="todos">TODOS</option>
                        @foreach ($tiposContrato as $contratos)
                        <option value="{{ $contratos->id }}">
                            {{ $contratos->descripcion }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón de Generar Reporte -->
                <div class="flex-1 min-w-[150px] flex gap-2">
                    <button type="button" id="generatePdfBtn"
                        class="mt-2 w-1/2 bg-red-100 text-red-500 px-4 py-2 rounded-md hover:bg-red-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500 flex items-center justify-center transition-all duration-200 ease-linear dark:bg-red-500/20 dark:hover:bg-red-500 dark:text-red-200 dark:hover:text-white">
                        <i class="ri-file-text-line mr-2"></i> PDF
                    </button>

                    <button type="button" id="generateExcelBtn"
                        class="mt-2 w-1/2 bg-green-100 text-green-600 px-4 py-2 rounded-md hover:bg-green-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center justify-center transition-all duration-200 ease-linear dark:bg-green-500/20 dark:hover:bg-green-600 dark:text-green-200 dark:hover:text-white">
                        <i class="ri-file-excel-2-line mr-2"></i> Excel
                    </button>
                </div>
            </div>
        </form>

        @endcan

        <div class="grid grid-cols-1 gap-x-5 w-full">
            <div class="2xl:col-span-3">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="flex items-center justify-center bg-purple-100 rounded-md size-12 dark:bg-purple-500/20 ltr:float-right rtl:float-left">
                            <i data-lucide="clock" class="text-purple-500 fill-purple-200 dark:fill-purple-500/30"></i>
                        </div>
                        <h5>Asistencia Individual</h5>

                        <div class="mb-4 mt-4 flex flex-col md:flex-row flex-wrap gap-4 md:gap-6 items-center">
                            <form id="formAsistencia" action="{{ route('asistencia.show', ':id') }}" method="GET"
                                class="flex flex-col md:flex-row items-end gap-4 w-full p-4 bg-white dark:bg-zink-700 rounded-lg shadow-sm">

                                <!-- Fecha inicio -->
                                <div class="flex-1 min-w-[180px]">
                                    <label for="fechaInicioReporte"
                                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Fecha inicio <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" required id="fechaInicioReporte" name="fechaInicioReporte"
                                        value="{{ $fechaInicio ?? old('fechaInicioReporte') }}" class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                placeholder:text-slate-400 dark:placeholder:text-zink-200 p-2 rounded-md" data-provider="flatpickr"
                                        data-date-format="d-m-Y" readonly="readonly" placeholder="DD-MM-YYYY">
                                </div>

                                <!-- Fecha fin -->
                                <div class="flex-1 min-w-[180px]">
                                    <label for="fechaFinReporte"
                                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Fecha fin <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" required id="fechaFinReporte" name="fechaFinReporte"
                                        value="{{ $fechaFin ?? old('fechaFinReporte') }}" class="w-full form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 
                disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 
                dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 
                placeholder:text-slate-400 dark:placeholder:text-zink-200 p-2 rounded-md" data-provider="flatpickr"
                                        data-date-format="d-m-Y" readonly="readonly" placeholder="DD-MM-YYYY">
                                </div>

                                <!-- Empleado -->
                                <div class="flex-1 min-w-[220px]">
                                    <label for="empleadoId"
                                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Empleado <span class="text-red-500">*</span>
                                    </label>
                                    <select id="empleadoId" name="empleado_id" required
                                        class="w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500
        dark:bg-zink-700 dark:border-zink-600 dark:placeholder-zink-300 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" data-choices
                                        data-choices-search-true>
                                        <option value="" selected disabled>Seleccione Empleado</option>
                                        @foreach ($empleados as $e)
                                        <option value="{{ $e->id }}"
                                            data-horario="{{ $e->horario ? $e->horario->descripcion : '' }}"
                                            {{ (isset($empleado) && $empleado->id == $e->id) || old('empleado_id') == $e->id ? 'selected' : '' }}>
                                            {{ $e->ci }} - {{ $e->apellido_pat }} {{ $e->apellido_mat }}
                                            {{ $e->nombres }}

                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Botón Buscar -->
                                <button type="submit" form="formAsistencia"
                                    class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:focus:ring-custom-400/20">
                                    Buscar
                                </button>
                            </form>

                        </div>

                        @if (isset($marcacionesFinales) && isset($empleado))
                        <div class="grid grid-cols-3 gap-5">
                            <!-- Botón de imprimir -->
                            <form action="{{ route('pdf-generate-asistencia') }}" method="POST" target="_blank">
                                @csrf
                                <input type="hidden" name="fecha_inicio" value={{ $fechaInicio }}>
                                <input type="hidden" name="fecha_fin" value={{ $fechaFin }}>
                                <!-- Campo oculto para enviar los datos -->
                                <input type="hidden" name="marcacionesFinales"
                                    value="{{ json_encode($marcacionesFinales) }}">

                                <input type="hidden" name="empleado" value="{{ json_encode($empleado) }}">
                                <button type="submit"
                                    class="text-red-500 bg-white border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:bg-zink-700 dark:hover:bg-red-500 dark:ring-red-400/20 dark:focus:bg-red-500">
                                    Imprimir Asistencia
                                </button>
                            </form>
                        </div>

                        <div class="grow flex flex-col items-center justify-center text-center space-y-2">
                            <h5 class="text-base font-semibold">
                                {{ $empleado->nombres }} {{ $empleado->apellido_pat }}
                                {{ $empleado->apellido_mat }}
                            </h5>

                            <h5 class="text-base">CI: {{ $empleado->ci }}</h5>

                            @foreach ($empleado->horarios as $index => $horario)
                            <h5 class="text-base">
                                Horario {{ $index + 1 }}: {{ $horario->descripcion }}
                            </h5>
                            <div class="text-base">
                                <p><strong>Ingreso 1:</strong> {{ $horario->ingreso1 }} -
                                    {{ $horario->salida1 }}</p>

                                @if (!empty($horario->ingreso2) && !empty($horario->salida2))
                                <p><strong>Ingreso 2:</strong> {{ $horario->ingreso2 }} -
                                    {{ $horario->salida2 }}</p>
                                @endif
                            </div>
                            @endforeach

                        </div>

                        <form action="{{ route('asistencia.update', ['asistencium' => $empleado->id]) }}" method="POST">

                            @csrf
                            @method('PUT')
                            @can('evaluacion_editar_marcacion')
                            <div class="mb-6 text-right">
                                <button type="button" id="editButton"
                                    class="text-purple-500 bg-white border-purple-500 btn hover:text-white hover:bg-purple-600 hover:border-purple-600 focus:text-white focus:bg-purple-600 focus:border-purple-600 focus:ring focus:ring-purple-100 active:text-white active:bg-purple-600 active:border-purple-600 active:ring active:ring-purple-100 dark:bg-zink-700 dark:hover:bg-purple-500 dark:ring-purple-400/20 dark:focus:bg-purple-500">
                                    Editar Marcaciones
                                </button>

                                <button type="submit" id="saveButton"
                                    class="hidden px-4 py-2 text-sm text-white bg-green-500 border-green-500 btn hover:text-white hover:bg-green-600 hover:border-green-600 focus:text-white focus:bg-green-600 focus:border-green-600 focus:ring focus:ring-green-100 active:text-white active:bg-green-600 active:border-green-600 active:ring active:ring-green-100 dark:ring-green-400/20">
                                    Guardar Cambios
                                </button>
                            </div>
                            @endcan

                            <div class="grid grid-cols-3 gap-5">
                                @php
                                $tiposMarcaciones = ['entrada_1', 'salida_1', 'entrada_2', 'salida_2'];
                                @endphp

                                @foreach ($marcacionesFinales as $index => $marcacion)
                                <!-- Campo oculto para el empleado -->
                                <input type="hidden" name="empleado" value="{{ $empleado['id'] ?? '' }}">

                                <!-- Campos ocultos para cada tipo de marcación -->
                                @foreach($tiposMarcaciones as $tipo)
                                @if(isset($marcacion[$tipo.'_id']) || isset($marcacion[$tipo.'_biometrico']))
                                <input type="hidden" name="marcaciones[{{ $index }}][{{ $tipo }}_id]"
                                    value="{{ $marcacion[$tipo.'_id'] ?? '' }}">
                                <input type="hidden" name="marcaciones[{{ $index }}][{{ $tipo }}_biometrico]"
                                    value="{{ $marcacion[$tipo.'_biometrico'] ?? '' }}">
                                @endif
                                @endforeach

                                <!-- Campo oculto para la fecha -->
                                <input type="hidden" name="marcaciones[{{ $index }}][fecha]"
                                    value="{{ $marcacion['fecha'] ?? '' }}">

                                <div class="flex flex-wrap p-4 border rounded-lg border-slate-200 dark:border-zink-500">
                                    <!-- Fecha -->
                                    <input type="text" name="marcaciones[{{ $index }}][fecha]"
                                        value="{{ \Carbon\Carbon::parse($marcacion['fecha'])->format('Y-m-d') }} ({{ \Carbon\Carbon::parse($marcacion['fecha'])->isoFormat('dddd') }})"
                                        readonly
                                        class="px-3 py-1 text-xs form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 bg-slate-100 dark:bg-zink-600 border-slate-300 dark:border-zink-500 dark:text-zink-200 text-slate-500 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full mb-2">

                                    <!-- Entradas y salidas -->

                                    <!-- Mostrar entrada_1 y salida_1 (como estaba originalmente) -->
                                    <div class="flex items-center space-x-2 w-full mb-2">
                                        <label for="entrada_1_{{ $index }}"
                                            class="text-sm text-slate-700 dark:text-zink-300">Entrada
                                            1:</label>
                                        <input type="time" name="marcaciones[{{ $index }}][entrada_1]"
                                            id="entrada_1_{{ $index }}" value="{{ $marcacion['entrada_1'] }}" readonly
                                            step="1"
                                            class="px-3 py-1 text-xs form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 bg-slate-100 dark:bg-zink-600 border-slate-300 dark:border-zink-500 dark:text-zink-200 text-slate-500 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-auto editable-field">
                                    </div>

                                    <div class="flex items-center space-x-2 w-full mb-2">
                                        <label for="salida_1_{{ $index }}"
                                            class="text-sm text-slate-700 dark:text-zink-300">Salida
                                            1:</label>
                                        <input type="time" name="marcaciones[{{ $index }}][salida_1]"
                                            id="salida_1_{{ $index }}" value="{{ $marcacion['salida_1'] }}" readonly
                                            step="1"
                                            class="px-3 py-1 text-xs form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 bg-slate-100 dark:bg-zink-600 border-slate-300 dark:border-zink-500 dark:text-zink-200 text-slate-500 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-auto editable-field">
                                    </div>

                                    <div class="flex items-center space-x-2 w-full mb-2">
                                        <label for="entrada_2_{{ $index }}"
                                            class="text-sm text-slate-700 dark:text-zink-300">Entrada
                                            2:</label>
                                        <input type="time" name="marcaciones[{{ $index }}][entrada_2]"
                                            id="entrada_2_{{ $index }}" value="{{ $marcacion['entrada_2'] }}" readonly
                                            step="1"
                                            class="px-3 py-1 text-xs form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 bg-slate-100 dark:bg-zink-600 border-slate-300 dark:border-zink-500 dark:text-zink-200 text-slate-500 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-auto editable-field">
                                    </div>

                                    <div class="flex items-center space-x-2 w-full mb-2">
                                        <label for="salida_2_{{ $index }}"
                                            class="text-sm text-slate-700 dark:text-zink-300">Salida 2:</label>
                                        <input type="time" name="marcaciones[{{ $index }}][salida_2]"
                                            id="salida_2_{{ $index }}" value="{{ $marcacion['salida_2'] }}" readonly
                                            step="1"
                                            class="px-3 py-1 text-xs form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 bg-slate-100 dark:bg-zink-600 border-slate-300 dark:border-zink-500 dark:text-zink-200 text-slate-500 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-auto editable-field">
                                    </div>

                                    <!-- Mostrar OBS si existe -->
                                    @if (!empty($marcacion['obs']))
                                    <div class="flex items-center space-x-2 w-full mb-2">
                                        <label for="obs_{{ $index }}"
                                            class="text-sm text-slate-700 dark:text-zink-300">Observación:</label>
                                        <input type="text" name="marcaciones[{{ $index }}][obs]" id="obs_{{ $index }}"
                                            value="{{ $marcacion['obs'] }}" readonly
                                            class="px-3 py-1 text-xs form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 bg-slate-100 dark:bg-zink-600 border-slate-300 dark:border-zink-500 dark:text-zink-200 text-slate-500 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full">
                                    </div>
                                    @endif

                                    <!-- Resto de entradas -->
                                    @if (count($marcacion['resto_de_entradas']) > 0)
                                    <select name="marcaciones[{{ $index }}][resto_entradas]"
                                        class="px-3 py-1 text-xs form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 bg-slate-100 dark:bg-zink-600 border-slate-300 dark:border-zink-500 dark:text-zink-200 text-slate-500 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 w-full editable-field"
                                        readonly>
                                        <option value="" selected disabled>Ingresos Totales</option>
                                        @foreach ($marcacion['resto_de_entradas'] as $i => $resto)
                                        <option value="{{ $resto['hora'] }}">{{ $resto['hora'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @else
                                    <p class="px-3 py-1 text-xs text-slate-500 dark:text-zink-200 w-full">
                                        No hay marcaciones extra.
                                    </p>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                        </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
</div>

@endsection