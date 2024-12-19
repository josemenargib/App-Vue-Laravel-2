<table>
    <thead>
    <tr>
        
        <th colspan="5">Batch {{$postulaciones->first()->batch->id }}</th>
        
    </tr>
    <tr>
        <th>Aprobados</th>
        <th>Reprobados</th>
        <th>Prueba</th>
        <th>Entrevista</th>
        <th>Total</th>

    </tr>
    </thead>
    <tbody>
        @php
        // Contadores de estados
        $aprobados = $postulaciones->where('estado', 'aprobado')->count();
        $reprobados = $postulaciones->where('estado', 'reprobado')->count();
        $prueba = $postulaciones->where('estado', 'prueba')->count();
        $entrevista = $postulaciones->where('estado', 'entrevista')->count();
        $total = $postulaciones->count();
    @endphp
     <tr>
        <!-- Mostramos los contadores -->
        <td>{{ $aprobados }}</td>
        <td>{{ $reprobados }}</td>
        <td>{{ $prueba }}</td>
        <td>{{ $entrevista }}</td>
        <td>{{ $total }}</td>
    </tr>

    
    </tbody>
</table>