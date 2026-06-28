<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('uatf/personal.jpg') }}">
    <title>Reporte de Vacaciones</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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
        }

        .img2 {
            background-image: url('uatf/personal.jpg');
            margin-left: auto;
        }

        h6 {
            margin: 2;
            line-height: 2;
        }

        h3,
        h4 {
            text-align: center;
            margin: 0;
        }

        .marcaciones {
            margin-top: 1em;
            font-size: 12px;
            border-collapse: collapse;
            width: 100%;
        }

        .marcaciones th,
        .marcaciones td {
            border: 1px solid #000000;
            padding: 4px;
            text-align: center;
        }

        .marcaciones th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .marcaciones tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .marcaciones tr:hover {
            background-color: #f1f1f1;
        }

        .status-aprobado {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-rechazado {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .info-empleado {
            width: 100%;
            margin-top: 1em;
            font-size: 13px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td width="11%">
                <div class="img1"></div>
            </td>
            <td width="60%">
                <h6>UNIVERSIDAD AUTÓNOMA TOMÁS FRÍAS</h6>
                <h6>DEPARTAMENTO DE PERSONAL</h6>
            </td>
            <td width="20%">
                <div class="img2"></div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <h3>CONTROL DE VACACIONES</h3>
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
    </table>

    @if ($todosEmpleados)
        <!-- Reporte para todos los empleados -->
        <table class="marcaciones">
            <thead>
                <tr>
                    <th>CI</th>
                    <th>Apellidos-Nombres</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th>Total Días</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($empleados as $emp)
                    @php
                        $empleadoVacaciones = $vacaciones
                            ->where('id_persona', $emp->id)
                            ->merge($vacaciones->where('solicitante_id', $emp->id))
                            ->sortBy('fecha_inicio');
                    @endphp

                    @if ($empleadoVacaciones->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron vacaciones</td>
                        </tr>
                    @else
                        @foreach ($empleadoVacaciones as $vacacion)
                            <tr>
                                <td>{{ $emp->ci }}</td>
                                <td>{{ $emp->apellido_pat }} {{ $emp->apellido_mat }} {{ $emp->nombres }}</td>
                                <td>{{ $vacacion->fecha_inicio }}</td>
                                <td>{{ $vacacion->fecha_fin }}</td>
                                <td class="{{ $vacacion->estado == 'aprobado' ? 'status-aprobado' : '' }} {{ $vacacion->estado == 'rechazado' ? 'status-rechazado' : '' }}">
                                    {{ ucfirst($vacacion->estado) }}
                                </td>
                                <td>{{ $vacacion->total_dias }}</td>
                                <td>{{ $vacacion->observacion ?? '' }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <!-- Reporte individual -->
        <div class="info-empleado">
            <table>
                <tr>
                    <td width="50%" class="text-left">
                        <strong>Id Empleado: </strong> {{ $empleado->ci }}
                    </td>
                    <td width="50%" class="text-right">
                        <strong>Horario: </strong> {{ $empleado->horario->descripcion }}
                    </td>
                </tr>
                <tr>
                    <td class="text-left">
                        <strong>Apellido y Nombres: </strong> {{ $empleado->apellido_pat }}
                        {{ $empleado->apellido_mat }} {{ $empleado->nombres }}
                    </td>
                    <td class="text-right">
                        <strong>Mes: </strong> {{ $mes }}
                    </td>
                </tr>
                <tr>
                    <td class="text-left">
                        <strong>Cargo: </strong> {{ $empleado->cargo->descripcion }}
                    </td>
                    <td class="text-right">
                        <strong>Año: </strong> {{ $anio }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="text-left">
                        <strong>Tipo de Contrato: </strong> {{ $empleado->tipoContrato->descripcion }}
                    </td>
                </tr>
            </table>
        </div>

        <table class="marcaciones">
            <thead>
                <tr>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th>Total Días</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @if ($vacaciones->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center">No se encontraron vacaciones</td>
                    </tr>
                @else
                    @foreach ($vacaciones as $vacacion)
                        <tr>
                            <td>{{ $vacacion->fecha_inicio }}</td>
                            <td>{{ $vacacion->fecha_fin }}</td>
                            <td class="{{ $vacacion->estado == 'aprobado' ? 'status-aprobado' : '' }} {{ $vacacion->estado == 'rechazado' ? 'status-rechazado' : '' }}">
                                {{ ucfirst($vacacion->estado) }}
                            </td>
                            <td>{{ $vacacion->total_dias }}</td>
                            <td>{{ $vacacion->observacion ?? '' }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    @endif
</body>

</html>