<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Marca y Modelo</title>
</head>

<body style="font-family: Arial, sans-serif; color: #1E293B;">
    <!-- Membrete Corporativo -->
    <table style="width: 100%; border-bottom: 3px solid #1E3A8A; margin-bottom: 15px;">
        <tr>
            <td colspan="4" style="font-size: 18pt; font-weight: bold; color: #1E3A8A; text-align: left;">
                INTERNAL MAIKEL CARS
            </td>
            <td colspan="3" style="font-size: 9pt; color: #64748B; text-align: right;">
                <strong>Fecha de Generación:</strong> {{ date('d/m/Y h:i A') }}
                @if(!empty($statusFilter))
                <br><strong>Filtro Estatus:</strong> {{ $statusFilter }}
                @endif
                @if(!empty($startDate) && !empty($endDate))
                <br><strong>Rango:</strong> {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="7" style="font-size: 13pt; font-weight: bold; color: #475569; text-align: left; padding-bottom: 8px;">
                INVENTARIO CONSOLIDADO POR MARCA Y MODELO
            </td>
        </tr>
    </table>

    <!-- Fila Vacia de Separacion -->
    <table><tr><td></td></tr></table>

    <!-- Tarjetas KPI / Resumen Gerencial -->
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="background-color: #F8FAFC; border: 1px solid #CBD5E1; font-weight: bold; color: #475569; text-align: center; font-size: 9pt;">MARCAS</td>
            <td style="background-color: #F8FAFC; border: 1px solid #CBD5E1; font-weight: bold; color: #475569; text-align: center; font-size: 9pt;">MODELOS</td>
            <td style="background-color: #F8FAFC; border: 1px solid #CBD5E1; font-weight: bold; color: #475569; text-align: center; font-size: 9pt;">PIEZAS EN STOCK</td>
            <td style="background-color: #F8FAFC; border: 1px solid #CBD5E1; font-weight: bold; color: #15803D; text-align: center; font-size: 9pt;">DISPONIBLES</td>
            <td style="background-color: #F8FAFC; border: 1px solid #CBD5E1; font-weight: bold; color: #475569; text-align: center; font-size: 9pt;">CONTENEDORES</td>
        </tr>
        <tr>
            <td style="background-color: #EFF6FF; border: 1px solid #CBD5E1; font-weight: bold; color: #1E3A8A; text-align: center; font-size: 14pt;">{{ number_format($kpis['total_marcas']) }}</td>
            <td style="background-color: #EFF6FF; border: 1px solid #CBD5E1; font-weight: bold; color: #1E3A8A; text-align: center; font-size: 14pt;">{{ number_format($kpis['total_modelos']) }}</td>
            <td style="background-color: #EFF6FF; border: 1px solid #CBD5E1; font-weight: bold; color: #1E3A8A; text-align: center; font-size: 14pt;">{{ number_format($kpis['total_piezas']) }}</td>
            <td style="background-color: #F0FDF4; border: 1px solid #CBD5E1; font-weight: bold; color: #15803D; text-align: center; font-size: 14pt;">{{ number_format($kpis['total_disponibles']) }}</td>
            <td style="background-color: #EFF6FF; border: 1px solid #CBD5E1; font-weight: bold; color: #1E3A8A; text-align: center; font-size: 14pt;">{{ number_format($kpis['total_contenedores']) }}</td>
        </tr>
    </table>

    <!-- Fila Vacia de Separacion -->
    <table><tr><td></td></tr></table>

    <!-- Tabla Principal de Datos -->
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #1E3A8A; font-size: 10pt; height: 28px;">MARCA</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #1E3A8A; font-size: 10pt; height: 28px;">MODELO / ESPECIFICACIÓN</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #1E3A8A; font-size: 10pt; height: 28px;">TIPO</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #1E3A8A; font-size: 10pt; height: 28px;">DISPONIBLES</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #1E3A8A; font-size: 10pt; height: 28px;">OTROS EST.</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #1E3A8A; font-size: 10pt; height: 28px;">TOTAL PIEZAS</th>
                <th style="background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #1E3A8A; font-size: 10pt; height: 28px;">CONTENEDORES DE ORIGEN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groups as $marca => $group)
                <!-- Encabezado de Marca -->
                <tr>
                    <td colspan="7" style="background-color: #DBEAFE; color: #1E3A8A; font-weight: bold; font-size: 11pt; border: 1px solid #93C5FD; padding: 6px;">
                        {{ $marca }} &nbsp;&mdash;&nbsp; ({{ $group['total_unidades'] }} piezas registradas)
                    </td>
                </tr>

                @foreach($group['modelos'] as $modelo => $m)
                    <tr>
                        <td style="background-color: #FFFFFF; color: #475569; font-size: 10pt; border: 1px solid #E2E8F0; text-align: left;">{{ $marca }}</td>
                        <td style="background-color: #FFFFFF; color: #0F172A; font-weight: bold; font-size: 10pt; border: 1px solid #E2E8F0; text-align: left;">{{ $m['modelo'] }}</td>
                        <td style="background-color: #FFFFFF; color: #334155; font-size: 10pt; border: 1px solid #E2E8F0; text-align: center;">{{ $m['tipo'] }}</td>
                        <td style="background-color: #FFFFFF; color: #15803D; font-weight: bold; font-size: 10pt; border: 1px solid #E2E8F0; text-align: center;">{{ number_format($m['disponibles']) }}</td>
                        <td style="background-color: #FFFFFF; color: #B45309; font-weight: bold; font-size: 10pt; border: 1px solid #E2E8F0; text-align: center;">{{ number_format($m['otros']) }}</td>
                        <td style="background-color: #FFFFFF; color: #0F172A; font-weight: bold; font-size: 10pt; border: 1px solid #E2E8F0; text-align: center;">{{ number_format($m['total']) }}</td>
                        <td style="background-color: #FFFFFF; color: #334155; font-size: 9.5pt; border: 1px solid #E2E8F0; text-align: left; white-space: pre-line;">{!! nl2br(e($m['contenedores_str'])) !!}</td>
                    </tr>
                @endforeach

                <!-- Subtotal de Marca -->
                <tr>
                    <td colspan="3" style="background-color: #F1F5F9; color: #0F172A; font-weight: bold; font-size: 10pt; border: 1px solid #CBD5E1; text-align: right;">
                        SUBTOTAL {{ $marca }}:
                    </td>
                    <td style="background-color: #F1F5F9; color: #15803D; font-weight: bold; font-size: 10pt; border: 1px solid #CBD5E1; text-align: center;">
                        {{ number_format($group['total_disponibles']) }}
                    </td>
                    <td style="background-color: #F1F5F9; color: #B45309; font-weight: bold; font-size: 10pt; border: 1px solid #CBD5E1; text-align: center;">
                        {{ number_format($group['total_otros']) }}
                    </td>
                    <td style="background-color: #F1F5F9; color: #0F172A; font-weight: bold; font-size: 10pt; border: 1px solid #CBD5E1; text-align: center;">
                        {{ number_format($group['total_unidades']) }}
                    </td>
                    <td style="background-color: #F1F5F9; color: #64748B; font-size: 10pt; border: 1px solid #CBD5E1; text-align: center;">
                        &mdash;
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="background-color: #FFFFFF; color: #64748B; font-size: 10pt; border: 1px solid #E2E8F0; text-align: center; padding: 15px;">
                        No se encontraron registros de inventario con los criterios seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if(!empty($groups))
        <tfoot>
            <!-- Totales Generales -->
            <tr>
                <td colspan="3" style="background-color: #1E293B; color: #FFFFFF; font-weight: bold; font-size: 11pt; border: 1px solid #0F172A; text-align: right;">
                    TOTALES GENERALES CONSOLIDADOS:
                </td>
                <td style="background-color: #1E293B; color: #22C55E; font-weight: bold; font-size: 11pt; border: 1px solid #0F172A; text-align: center;">
                    {{ number_format($kpis['total_disponibles']) }}
                </td>
                <td style="background-color: #1E293B; color: #F59E0B; font-weight: bold; font-size: 11pt; border: 1px solid #0F172A; text-align: center;">
                    {{ number_format($kpis['total_otros']) }}
                </td>
                <td style="background-color: #1E293B; color: #FFFFFF; font-weight: bold; font-size: 11pt; border: 1px solid #0F172A; text-align: center;">
                    {{ number_format($kpis['total_piezas']) }}
                </td>
                <td style="background-color: #1E293B; color: #38BDF8; font-weight: bold; font-size: 11pt; border: 1px solid #0F172A; text-align: center;">
                    {{ number_format($kpis['total_contenedores']) }} Contenedores
                </td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>

</html>
