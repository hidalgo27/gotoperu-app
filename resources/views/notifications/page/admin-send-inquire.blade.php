<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; font-family: Arial, sans-serif; padding: 20px;">
    <tbody>
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; padding: 20px;">
                <tbody>
                <!-- Logo centrado -->
                <tr>
                    <td align="center" style="padding-bottom: 20px;">
                        <img alt="logo gotoperu" src="{{asset('images/logo-ave.png')}}" width="150" style="display: block;">
                    </td>
                </tr>

                <!-- Información del mensaje -->
                <tr>
                    <td style="color: #333333; font-size: 16px; line-height: 24px; padding-bottom: 20px;">
                        <p style="font-weight: bold;">Mensaje de: {{$nombre}}</p>
                    </td>
                </tr>

                <!-- Tabla de datos con dos columnas -->
                <tr style="padding-bottom: 20px">
                    <td>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tbody>
                            <tr>
                                <!-- Columna izquierda -->
                                <td width="50%" style="vertical-align: top; padding-right: 10px; color: #666666;">
                                    <p><strong>Package:</strong> {{$package}}</p>
                                    <p><strong>Category Hotel:</strong> {{$category}}</p>
                                    <p><strong>Destinations:</strong> {{$destination}}</p>
                                    <p><strong>Travellers:</strong> {{$travellers}}</p>
                                    <p><strong>Duration:</strong> {{$duration}}</p>
                                </td>

                                <!-- Columna derecha -->
                                <td width="50%" style="vertical-align: top; padding-left: 10px; color: #666666;">
                                    <p><strong>Email:</strong> {{$email}}</p>
                                    <p><strong>Phone:</strong> {{$country}} {{$telefono}}</p>
                                    <p><strong>Travel date:</strong> {{$fecha}}</p>
                                    <p><strong>Comment:</strong> {{$comentario}}</p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding: 10px"></td>
                </tr>

                <!-- Pie de página con el dominio y el producto -->
                <tr>
                    <td align="center" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0e0e0; color: #999999; font-size: 14px;">
{{--                        <p style="margin: 0;">Visita nuestro sitio: <a href="{{$domain}}" target="_blank" style="color: #007bff; text-decoration: none;">{{$product}}</a></p>--}}
                        <p style="margin: 5px 0;">&copy; {{ date('Y') }} GOTOGROUP. Todos los derechos reservados.</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
