@extends('layouts.notifications.app-inquire')
@section('content')

    <tr>
        <td style="padding:20px 0px 20px 50px">
            <p style="font-size:18px"><b>Hi, {{$nombre}}</b></p>
            <center style="background:#f6f6f6; padding:10px;">
                <table>
                    <tbody>
                    <tr>
                        <td style="text-align:center">
                            <p>Gracias por contactar. Un asesor se contactara con usted a la brevedad.</p>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </center>
        </td>
    </tr>

@stop
