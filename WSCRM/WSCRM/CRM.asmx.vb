Imports System.Web.Services
Imports System.Web.Services.Protocols
Imports System.Web.Script.Services
Imports System.Web.Script.Serialization
Imports System.ComponentModel
Imports System.Web.Script
Imports System.Data.SqlClient
Imports Newtonsoft.Json

' Para permitir que se llame a este servicio web desde un script, usando ASP.NET AJAX, quite la marca de comentario de la línea siguiente.
' <System.Web.Script.Services.ScriptService()> _

<System.Web.Services.WebService(Namespace:="http://tempuri.org/")> _
<System.Web.Services.WebServiceBinding(ConformsTo:=WsiProfiles.BasicProfile1_1)> _
<ToolboxItem(False)> _
Public Class WebService1
    Inherits System.Web.Services.WebService
    Dim strCon As String = "Provider=SQLOLEDB;Data Source=127.0.0.1\DEV;User ID=gesteybar;Password=123456;Initial Catalog=crmdev"
    <WebMethod()> _
    <ScriptMethod(ResponseFormat:=ResponseFormat.Json, UseHttpGET:=True)> _
    Public Sub CustomQuery(Cadena As String, Tipo As String)

        Context.Response.ContentType = "application/json"
        If Tipo = "Q" Then
            Context.Response.Write(LeerDatos(Cadena))
        Else
            Context.Response.Write(Ejecutar(Cadena))
        End If

        Context.Response.End()

    End Sub
    <WebMethod()> _
    <ScriptMethod(ResponseFormat:=ResponseFormat.Json, UseHttpGET:=True)> _
    Public Sub LeerUsuarios(user As String)

        Dim Cadena As String = "exec SP_ReadUsers '" & user & "'"


        Context.Response.ContentType = "application/json"
        Context.Response.Write(LeerDatos(Cadena))
        Context.Response.End()



    End Sub
    <WebMethod()> _
    <ScriptMethod(ResponseFormat:=ResponseFormat.Json, UseHttpGET:=True)> _
    Public Sub LogIn(user As String, pass As String)
        Dim Cadena As String = "exec SP_LogIn '" & user & "', '" & pass & "'"

        Context.Response.ContentType = "application/json"
        Context.Response.Write(LeerDatos(Cadena))
        Context.Response.End()


    End Sub
    <WebMethod()> _
    <ScriptMethod(ResponseFormat:=ResponseFormat.Json, UseHttpGET:=True)> _
    Public Sub Acceso(user As String, modulo As String)
        Dim Cadena As String = "exec SP_Acceso " & user & ", " & modulo

        Context.Response.ContentType = "application/json"
        Context.Response.Write(LeerDatos(Cadena))
        Context.Response.End()


    End Sub
    <WebMethod()> _
    <ScriptMethod(ResponseFormat:=ResponseFormat.Json, UseHttpGET:=True)> _
    Public Sub cargarUsuarios()
        Dim Cadena As String = "select idUsuario, Login, Nombre, Estado from Usuarios u left join Estados e on u.idEstado=e.idEstado order by Login asc"

        Context.Response.ContentType = "application/json"
        Context.Response.Write(LeerDatos(Cadena))
        Context.Response.End()


    End Sub
    <WebMethod()> _
    <ScriptMethod(ResponseFormat:=ResponseFormat.Json, UseHttpGET:=True)> _
    Public Sub CambiarPass(user As Integer, pass As String)
        Dim Cadena As String = "exec SP_CambiaPass " & user & ", '" & pass & "'"

        Context.Response.ContentType = "application/json"
        Context.Response.Write(LeerDatos(Cadena))
        Context.Response.End()


    End Sub
    <WebMethod()> _
    <ScriptMethod(ResponseFormat:=ResponseFormat.Json, UseHttpGET:=True)> _
    Public Sub IngresarUsuario(idUsuario As Integer, idSector As Integer, idEstado As Integer, Login As String, Pass As String, ZUSCOD As String, Nombre As String, Mail As String, Cambia As String)
        Try
            Dim Cadena As String = "exec SP_InsertUser " & idUsuario & ", " & idSector & ", " & idEstado & ", '" & _
                Login & "', '" & Pass & "', '" & ZUSCOD & "', '" & Nombre & "','" & Mail & "', '" & Cambia & "'"

            Context.Response.ContentType = "application/json"
            Context.Response.Write(LeerDatos(Cadena))
            Context.Response.End()

        Catch ex As Exception
            Context.Response.ContentType = "application/json"
            Context.Response.Write(ex.Message)
            Context.Response.End()

        End Try


    End Sub
    <WebMethod()> _
    <ScriptMethod(ResponseFormat:=ResponseFormat.Json, UseHttpGET:=True)> _
    Public Sub BlockUser(user As Integer, autor As Integer)
        Dim Cadena As String = "exec SP_CambiaEstado " & user & ", '" & autor & "'"

        Context.Response.ContentType = "application/json"
        Context.Response.Write(LeerDatos(Cadena))
        Context.Response.End()


    End Sub
    <WebMethod()> _
    <ScriptMethod(ResponseFormat:=ResponseFormat.Json, UseHttpGET:=True)> _
    Public Sub DeleteUser(user As Integer, autor As Integer)
        Dim Cadena As String = "exec SP_EliminaUsuario " & user & ", '" & autor & "'"

        Context.Response.ContentType = "application/json"
        Context.Response.Write(LeerDatos(Cadena))
        Context.Response.End()


    End Sub

    Function LeerDatos(query As String) As String

        Dim C As New ADODB.Connection, R As New ADODB.Recordset
        C.ConnectionString = strCon
        C.Open()

        Dim serializer As New System.Web.Script.Serialization.JavaScriptSerializer()
        serializer.MaxJsonLength = 50000000
        Dim rows As New List(Of Dictionary(Of String, Object))()
        Dim context As New HttpContext(HttpContext.Current.Request,
        HttpContext.Current.Response)
        context.Response.ContentType = "application/json;charset=utf-8"
        context.Response.AppendHeader("Access-Control-Allow-Origin", "*")

        Dim row As Dictionary(Of String, Object) = Nothing

        Dim Cadena As String = query



        Try
            R.Open(Cadena, C, ADODB.CursorTypeEnum.adOpenForwardOnly)
            While Not R.EOF
                row = New Dictionary(Of String, Object)()
                For Each f As Object In R.Fields
                    row.Add(f.name, f.value)
                Next
                rows.Add(row)
                R.MoveNext()
            End While


        Catch ex As Exception
            MsgBox(ex.Message)

        End Try

        Return JsonConvert.SerializeObject(rows, Newtonsoft.Json.Formatting.Indented)

    End Function

    Function Ejecutar(query As String) As String
        Dim context As New HttpContext(HttpContext.Current.Request,
        HttpContext.Current.Response)
        Dim Resp As String
        context.Response.ContentType = "application/json;charset=utf-8"
        context.Response.AppendHeader("Access-Control-Allow-Origin", "*")


        Dim C As New ADODB.Connection, R As New ADODB.Recordset
        C.ConnectionString = strCon
        C.Open()


        Try
            C.Execute(query)

            Resp = "ok"
        Catch ex As Exception

            Resp = ex.Message
        End Try

        Return Resp


    End Function


End Class
Public Class Persona
    Public Property Nombre As String
    Public Property DNI As String
End Class