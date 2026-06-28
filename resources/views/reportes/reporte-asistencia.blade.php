<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asistencia</title>

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-content {
            display: flex;
            align-items: center;
            /* Centra verticalmente la imagen y el texto */
            gap: 10px;
            /* Espaciado entre la imagen y los textos */
        }

        .img1,
        .img2 {
            width: 80px;
            height: 80px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .img1 {
            background-image: url('uatf/AUTF.png');
            /* Ruta de la primera imagen */
        }

        .img2 {
            background-image: url('uatf/personal.jpg');
            /* Ruta de la segunda imagen */
            margin-left: auto;
            /* Alinea la segunda imagen a la izquierda */
        }

        h6 {
            margin: 2;
            line-height: 2;
            /* Espaciado entre líneas */
        }

        h4 {
            text-align: center;
            /* Centra el título en su celda */
            margin: 0;
        }


        p {
            font-size: 10px margin: 0;
        }

        .marcaciones {
            margin-top: 1px;
            font-size: 12px;
            border-collapse: collapse;
            /* Evita bordes dobles */
            width: 100%;
            /* Asegura que la tabla ocupe todo el ancho disponible */
        }

        .marcaciones th,
        .marcaciones td {
            border: 1px solid #000000;
            /* Bordes de las celdas */
            text-align: left;
            /* Alinea el texto a la izquierda */
        }

        .marcaciones th {
            background-color: #f2f2f2;
            /* Fondo gris claro para los encabezados */
            font-weight: bold;
            /* Enfatiza el texto en los encabezados */
        }

        .marcaciones tr:nth-child(even) {
            background-color: #f9f9f9;
            /* Fondo alterno para las filas pares */
        }

        .marcaciones tr:hover {
            background-color: #f1f1f1;
            /* Fondo al pasar el cursor por encima de una fila */
        }

        .marcaciones td {
            font-size: 12px;
            /* Aumenta el tamaño de texto en las celdas para mayor legibilidad */
            padding: 2;
            text-align: center;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <!-- Imagen y textos en la misma fila -->
            <td width="11%">
                <div class="img1"></div>
            </td>

            <td width="60%">
                <h6>UNIVERSIDAD AUTÓNOMA TOMÁS FRÍAS</h6>
                <h6>DEPARTAMENTO DE PERSONAL</h6>
            </td>
            <td width=20%>
                <div class="img2"></div>
            </td>
        </tr>

        <!-- Título centrado -->
        <tr>
            <td colspan="3">
                <h4>KARDEX DE CONTROL DE ASISTENCIA</h4>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <p style="margin: 0;text-align: center;">
                    <strong>Desde:</strong> {{ $primeraFecha }}
                    <strong>Hasta:</strong> {{ $ultimaFecha }}
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <h5 style="margin: 0;text-align: center;">TIPO DE CONTRATO {{ $empleado->tipoContrato->descripcion }}
                </h5>
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top:1em;">
        <tr>
            <!-- Imagen y textos en la misma fila -->
            <td width="50%">
                <strong>Id Empleado: </strong> {{ $empleado->ci }}
            </td>

            <td width="50%" style="text-align: right;">
                 @foreach ( $empleado->horarios as $horario )
                <strong>Horario: </strong>    {{ $horario->descripcion }} <br>
                @endforeach
            </td>
        </tr>
        <tr>
            <!-- Imagen y textos en la misma fila -->
            <td width="50%">
                <strong>Apellido y Nombres: </strong> {{ $empleado->apellido_pat }} {{ $empleado->apellido_mat }}
                {{ $empleado->nombres }}
            </td>

            <td width="50%" style="text-align: right;">
                <strong>Mes: </strong> {{ $mes }}
            </td>
        </tr>

        <tr>
            <!-- Imagen y textos en la misma fila -->
            <td width="50%">
                <strong>Cargo: </strong> {{ $empleado->cargo->descripcion }}
            </td>

            <td width="50%" style="text-align: right;">
                <strong>Año: </strong> {{ $anio }}
            </td>
        </tr>
    </table>

    <table class="marcaciones">
        <thead>
            <td>Fecha</td>
            <td>Dia</td>
            <td>ingreso1</td>
            <td>salida1</td>
            <td>ingreso2</td>
            <td>salida2</td>
            <td>Horas Trabajo</td>
            <td>Atrasos</td>
            <td>Abandono</td>
            <td>Faltas</td>
            <td>Dias Trabajo</td>
            <td>Observaciones</td>
        </thead>
        <tbody>
            @php
                // Inicializar variables para acumular los totales
                $totalAtraso = 0;
                $totalAbandono = 0;
                $totalFaltas = 0;
                $totalDiasTrabajo = 0;
            @endphp
           @foreach ($marcacionesFinales as $marcacion)
    @if (!isset($marcacion->dia))
        @continue
    @endif
    <tr>
        <td>{{ $marcacion->fecha }}</td>
        <td>{{ $marcacion->dia }}</td>
        
        @if(isset($marcacion->entrada_3) && isset($marcacion->salida_3))
            <!-- Mostrar entrada_3 y salida_3 como horario principal -->
            <td>{{ $marcacion->entrada_3 }}</td>
            <td>{{ $marcacion->salida_3 }}</td>
            <td colspan="2" class="text-center">-</td> <!-- Ocultar entrada_2/salida_2 -->
        @else
            <!-- Mostrar horario normal -->
            <td>{{ $marcacion->entrada_1 ?? '00:00:00' }}</td>
            <td>{{ $marcacion->salida_1 ?? '00:00:00' }}</td>
            <td>{{ $marcacion->entrada_2 ?? '00:00:00' }}</td>
            <td>{{ $marcacion->salida_2 ?? '00:00:00' }}</td>
        @endif
        
        <td>{{ $marcacion->horas_trabajo ?? '' }}</td>
        <td>{{ $marcacion->atrasos ?? '' }}</td>
        <td>{{ $marcacion->abandono ?? '' }}</td>
        <td>{{ $marcacion->faltas ?? '' }}</td>
        <td>{{ $marcacion->dias_trabajo ?? '' }}</td>
<td style="width: 130px;">{{ Str::lower($marcacion->observaciones ?? '') }}</td>    </tr>
    @php
        // Acumular los valores para los totales
        $totalAtraso += $marcacion->atrasos ?? 0;
        $totalAbandono += $marcacion->abandono ?? 0;
        $totalFaltas += $marcacion->faltas ?? 0;
        $totalDiasTrabajo += $marcacion->dias_trabajo ?? 0;
    @endphp
@endforeach
            <tr>
                <td colspan="7"> Totales</td>
                <td colspan="">{{ $totalAtraso }}</td> <!-- Total de abandono -->
                <td colspan="">{{ $totalAbandono }}</td> <!-- Total de abandono -->
                <td colspan="">{{ $totalFaltas }}</td> <!-- Total de faltas -->
                <td colspan="">{{ $totalDiasTrabajo }}</td> <!-- Total de días trabajados -->
                <td colspan=""></td>

            </tr>
        </tbody>
    </table>


    <table>
        <tr>
            <td>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                        <div class="d-flex align-items-center gap-1">
                            <strong>Días Bono Té:</strong>
                            <span class="badge bg-primary">{{ $totalDiasTrabajo }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <strong>Abandonos:</strong>
                            <span class="badge bg-danger">{{ $totalAbandono }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <strong>Faltas:</strong>
                            <span class="badge bg-warning text-dark">{{ $totalFaltas }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <strong>Atrasos:</strong>
                            <span class="badge bg-secondary">{{ $totalAtraso }}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                        <div class="d-flex align-items-center gap-1">
                            <strong>Cuenta Haber:</strong>
                            <span class="badge bg-info">{{ $empleado->total_dias_vacacion }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <strong>Vacaciones:</strong>
                            <span class="badge bg-success">{{ $vacaciones }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <strong>Permisos:</strong>
                            <span class="badge bg-dark">{{ $permisos }}</span>
                        </div>
                    </div>
                </div>
            </td>


        </tr>


    </table>




</body>

</html>
