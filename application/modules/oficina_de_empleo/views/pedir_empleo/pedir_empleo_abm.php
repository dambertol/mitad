<!--
        /*
         * Vista ABM de curriculum.
         * Autor: Leandro
         * Creado: 10/10/2018 (Leandro)
         * Modificado: 16/10/2018 (Pablo)
         */
-->
<style> legend.group-border {
        width: inherit;
        /* Or auto */
        padding: 0 10px;
        /* To give a bit of padding on the left and right */
        border-bottom: none;
        margin-bottom: 0px;
    }
    fieldset.group-border {
        border: 1px groove #ddd !important;
        padding: 0 1.4em 1.4em 1.4em !important;
        margin: 0 0 1.5em 0 !important;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
    }
</style>
<?php if (!empty($error)) : ?>
    <div class="alert alert-danger alert-dismissible fade in alert-fixed" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>ERROR!</strong><?php echo $error; ?>
    </div>
<?php endif; ?>
<?php if (!empty($message)) : ?>
    <div class="alert alert-success alert-dismissible fade in alert-fixed" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>OK!</strong><?php echo $message; ?>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><?php echo (!empty($title_view)) ? $title_view : 'pedir_empleo'; ?></h2>
                <?php if (!empty($audi_modal)): ?>
                    <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#audi-modal">
                        <i class="fa fa-info-circle"></i>
                    </button>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php $data_submit = ($txt_btn === 'Eliminar') ? array('class' => 'btn btn-danger btn-sm', 'title' => $txt_btn) : array('class' => 'btn btn-primary btn-sm', 'title' => $txt_btn); ?>
                <?php echo form_open(uri_string(), 'class="form-horizontal"'); ?>
                <div class="row">
                    
<!--  ********************************aca ocurre la magia **************** ************************-->
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['cuil']['label']; ?> 
                                <?php echo $fields['cuil']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['Dni']['label']; ?> 
                                <?php echo $fields['Dni']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['nombre']['label']; ?> 
                                <?php echo $fields['nombre']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['apellido']['label']; ?> 
                                <?php echo $fields['apellido']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['telefono']['label']; ?> 
                                <?php echo $fields['telefono']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['email']['label']; ?> 
                                <?php echo $fields['email']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['genero']['label']; ?> 
                                <?php echo $fields['genero']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['fecha_nac']['label']; ?> 
                                <?php echo $fields['fecha_nac']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['domicilio']['label']; ?> 
                                <?php echo $fields['domicilio']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <label for="distrito" class="control-label col-sm-3">Distrito</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="distrito" class="form-control selectpicker distrito" id="distrito" data-live-search="true" title="-- Seleccionar --" tabindex="null">
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                </div> 
                            </div>
                    </div>

                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['otro_cel']['label']; ?> 
                                <?php echo $fields['otro_cel']['form']; ?> 
                    </div>
                    </div>

                <div class="ln_solid"></div>
                <div class="row">
                    <div class="border-group form-gruop">
                    <div class="change_col col-md-6 form-group">
                        <div class="dropdown bootstrap-checkbox form-control bs3">
                                <?php echo $fields['capacitacion']['label']; ?> 
                                <input type='checkbox' name="capacitacion" class="selectpicker" id="capacitacion" value=1>
                        </div>
                    </div>

                    <div class="change_col col-md-6 form-group">
                        <label for="horario_cap" class="control-label col-sm-3">Horario disponible</label> 
                        <div class="col-sm-9">
                        <div class="dropdown bootstrap-select form-control bs3">
                            <select name="horario_cap" class="form-control selectpicker" id="horario_cap" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                <option class="bs-title-option" value=""></option>
                                <option value="mañana">mañana</option>
                                <option value="tarde" >tarde</option>
                                <option value="noche">noche</option>
                            </select>
                        </div></div>
                    </div>

                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['intereses_cap']['label']; ?> 
                                <?php echo $fields['intereses_cap']['form']; ?> 
                    </div>
                </div>
            </div>

            <div class="ln_solid"></div>
                <div class="row">
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['busca_empleo']['label']; ?> 
                                <input type='checkbox' name="busca_empleo" class="selectpicker" id="busca_empleo" value="s">
                    </div>
                   
                    <div class="change_col col-md-6 form-group">
                                <label for="interes_lab" class="control-label col-sm-3">Intereses laborales</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="interes_lab" class="form-control selectpicker interes_lab" id="interes_lab" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                </div> 
                            </div>
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <label for="disponib_lab" class="control-label col-sm-3">Disponibilidad horaria</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="disponib_lab" class="form-control selectpicker disponib_lab" id="disponib_lab" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                </div> 
                            </div>
                    </div>

                    <div class="change_col col-md-6 form-group">
                                <label for="condic" class="control-label col-sm-3">condiciones especiales de trabajo</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="condic" class="form-control selectpicker condic" id="condic" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                </div> 
                            </div>
                    </div>
                 </div>

                <div class="ln_solid"></div>
                <div class="row">
                    <div class="border-group form-gruop">
                    <div class="change_col col-md-6 form-group">
                                <label for="movilidad" class="control-label col-sm-3">Vehiculo propio</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="movilidad" class="form-control selectpicker movilidad" id="movilidad" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                </div> 
                            </div>
                    </div>
                 
                    <div class="change_col col-md-6 form-group">
                                <label for="movil_carnet" class="control-label col-sm-3">Carnet de conducir</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="movil_carnet" class="form-control selectpicker movil_carnet" id="movil_carnet" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                </div> 
                            </div>
                    </div>
                    </div>
                    </div> 

                    <div class="ln_solid"></div>
                <div class="row">
                    <div class="border-group">
                    <div class="change_col col-md-6 form-group">
                                <label for="discapacidad" class="control-label col-sm-3">Tipo de discapacidad</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="discapacidad" class="form-control selectpicker discapacidad" id="discapacidad" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                </div> 
                            </div>
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['cud']['label']; ?> 
                                <?php echo $fields['cud']['form']; ?> 
                    </div>
                    </div> </div>
                <div class="ln_solid"></div>
                <div class="row">
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['estudio']['label']; ?> 
                                <?php echo $fields['estudio']['form']; ?> 
                    </div>

                    <div class="change_col col-md-6 form-group">
                                <label for="estudiosOt" class="control-label col-sm-3">Titulo secundario</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="estudiosOt" class="form-control selectpicker titsecundario" id="estudiosOt" data-live-search="true" title="-- Seleccionar --" tabindex="null" >
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                </div> 
                            </div>
                    </div>

                    <div class="change_col col-md-6 form-group">
                                <label for="grado" class="control-label col-sm-3">Titulo de grado</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="grado" class="form-control selectpicker grado" id="grado" data-live-search="true" title="-- Seleccionar --" tabindex="null" >
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                </div> 
                            </div>
                    </div>
                    
                    <div class="change_col col-md-6 form-group">
                                <label for="gradoo" class="control-label col-sm-3">Rubro</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="gradoo" class="form-control selectpicker gradoo" id="gradoo" data-live-search="true" title="-- Seleccionar --" tabindex="null" >
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                </div> 
                            </div>
                    </div>

                <div class="border-group form-gruop">
                    
                    <div id="poliglota">
                    <div class="change_col col-md-6 form-group" >
                            <label for="idioma" class="control-label col-sm-3">Idiomas</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="idioma" class="form-control selectpicker idiomas idioma" id="idioma" data-live-search="true" title="-- Seleccionar --" tabindex="null">
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                    </div> 
                                </div>
                            <label for="idiomaN" class="control-label col-sm-3">Nivel</label> 
                            <div class="col-sm-9">
                                <input name="idiomaN" value="" tipe="number" maxlength="1" id="idiomaN" class="form-control Nidioma idioma">
                                <button id="addCampo" type="button" class="btn btn-info">Agregar idioma</button>
                            </div>
                    </div>
                </div>
                    </div>
                    </div>
                    <div class="border-group form-gruop">
                    <div id="tecno">
                    <div class="change_col col-md-6 form-group" >
                            <label for="computacio" class="control-label col-sm-3">programas de computacion</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="computacio" class="form-control selectpicker computacio" id="computacio" data-live-search="true" title="-- Seleccionar --" tabindex="null">
                                        <option class="bs-title-option" value=""></option>
                                    </select>
                                    </div> 
                                </div>
                            <label for="computacioN" class="control-label col-sm-3">Nivel</label> 
                            <div class="col-sm-9">
                                <input name="computacioN" value="" tipe="number" maxlength="1" id="computacioN" class="form-control Ncomputacio">
                                <button id="addCampoCompu" type="button" class="btn btn-info">Agregar programa</button>
                            </div>
                    </div>
                </div>

                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['cursos']['label']; ?> 
                                <?php echo $fields['cursos']['form']; ?> 
                    </div>
                
                    <div class="change_col col-md-6 form-group">
                            <label for="oficios" class="control-label col-sm-3">Oficios</label> 
                                <div class="col-sm-9"><div class="dropdown bootstrap-select form-control bs3">
                                    <select name="oficios" class="form-control selectpicker oficios" id="oficios" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                        <option class="bs-title-option" value="" ></option>
                                    </select>
                            </div> 
                    </div>
                </div>

                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['experiencia']['label']; ?> 
                                <?php echo $fields['experiencia']['form']; ?> 
                    </div>
                </div>
                <div class="ln_solid"></div>
                <div class="row">
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['exmuni']['label']; ?> 
                                <input type='checkbox' name="busca_empleo" class="selectpicker" id="busca_empleo" value="s">       
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['famimuni']['label']; ?> 
                                <input type='checkbox' name="busca_empleo" class="selectpicker" id="busca_empleo" value="s">
                    </div> 
                </div>

                <div class="ln_solid"></div>
                <div class="row">
                    <div class="group-border ">
                    <div class="change_col col-md-9 form-group">
                                <?php echo $fields['aclaraciones']['label']; ?> 
                                <?php echo $fields['aclaraciones']['form']; ?> 
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <?php echo $fields['pdf']['label']; ?> 
                                <?php echo $fields['pdf']['form']; ?> 
                    </div>
                    </div>

                </div>
                <div class="ln_solid otro"></div>
                <div class="text-center">
                    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                    <?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $empleo->Dni) : ''; ?>
                    <a href="oficina_de_empleo/pedir_empleo/listar" class="btn btn-default btn-sm">Cancelar</a> 
                </div>

                <script>//esto completa los campos para ser leidos por la base de datos peeero no los va a leer correctamente para la edicion y lectura 
                    
                    let idioma=document.querySelectorAll(".idioma");
                    let idiomaN=document.querySelectorAll(".Nidioma");
                    let compu=document.querySelectorAll(".computacio");
                    let compuN=document.querySelectorAll(".Ncomputacio");
                    const conten=document.querySelectorAll(".otro");

                    let compilador=function(arra1,arra2,nombre,conten){
                        let compilado;
                        for (let index = 0; index < idioma.length; index++) {
                             compilado += arra1[index]["value"]+" "+(arra2[index]["value"]?arra2[index]["value"]:" ")+" - " ;
                             let newInput= document.createElement("INPUT");
                            newInput.setAttribute("name",nombre);
                            newInput.setAttribute("value",compilado);
                            newInput.setAttribute("hidden","true");
                            newInput.setAttribute("id",nombre);
                            conten.appendChild(newInput);
                    };
                    };
                    let relleno=function(){
                        compilador(idioma,idiomaN,"idiomas",conten);
                        compilador(compu,compuN,"computacion",conten);    
                    };
                    window.onbeforeunload = relleno;

                </script>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo (!empty($audi_modal) ? $audi_modal : ''); ?>

<script>
    $(document).ready(function () {
        //Modificar el style por defecto
        $('form .change_col').find('div.col-sm-10').removeClass('col-sm-10').addClass('col-sm-9');
        $('form .change_col').find('label.col-sm-2').removeClass('col-sm-2').addClass('col-sm-3');
        $('.obs').find('div.col-sm-10').removeClass('col-sm-10').addClass('col-sm-12');
    });
</script>

<script type="text/javascript">

</script>

<script>
    const grado_carr = ['Analista','Auxiliar','Doctorado','Ingeniería','Licenciatura','Maestro','Profesorado','Tecnicatura'];
    const carreras = ['Abogacía','Acompañante Terapeutico','Administración','Administración Contable','Agente Sanitario','Agronomía','Anestesista','Arquitectura','Bioimágen','Bioquímica','Bromatología',
    'Ciencias Políticas y Administración Pública','Cine y Video','Civil','Comercio Exterior','Comunicación Digital','Comunicación Digital','Comunicación Social','Contador','Criminología',
    'Diagnóstico por Imágen','Diseño Grafico','Diseño Industrial','Diseño y Animación','Economía','Educación Física','Educación Inicial','Educación Primaria','Educación Secundaria','Educación Terciaria/Universitaria',
    'Electromecánica','Electrónica','Enfermería','Enología','Farmacia','Fonoaudiología','Gastronomía','Geología','Gestión Ambiental','Gestor','Grafología','Guía Turismo','Higiene y Seguridad Laboral',
    'Hotelería y Turismo','Industrial','Instrumentista Quirúrgico','Internacionales','Jardin Maternal','Kinesiología','Laboratorio','Letras','Logística y Transporte','Marketing','Martillero Público, Corredor Inmobiliario',
    'Masoterapia','Mecánica y Producción Automatizada','Medicina','Metalmecánico','Minería y Gas Sustentable','Niñez, Adolescencia y Familia','Nutrición','Obstetricia','Odontología','Otros',
    'Petróleo y Gas','Programador','Protesis Dental','Psicología','Psicopedagogía','Publicidad','Química Industrial','Radiología','Recursos Humanos','Recursos Naturales','Relaciones Humanas',
    'Seguridad Pública y Penitenciaría','Sistemas','Sonido','Trabajo Social'];
    const idiomas=['Albanés', 'Alemán', 'Arabe', 'Bielorruso', 'Búlgaro', 'Catalán', 'Checo', 'Chino', 'Coreano', 'Croata',
    'Danés', 'Eslovaco', 'Esloveno', 'Español', 'Estonio', 'Frances', 'Filandés', 'Griego', 'Húngaro', 'Idish', 'Indonesio',
    'Indonesio', 'Inglés', 'Islandés', 'Italiano','Japonés', 'Ladino', 'Latín', 'Letón', 'Lituano', 'Neerlandés', 'Noruego',
    'Polaco', 'Portugues', 'Rumano', 'Ruso', 'Serbio', 'Sueco', 'Turco', 'Ucraniano', 'Vasco'];
    const oficios=['Administración','Atención al Público','Bachero','Barbero','Bodega','Cadete','Cajero','Carpintería','Chapería y Pintura',
    'Chofer','Cosecha','Cocina','Community Manager','Construcción','Control de Stock','Costura/Textil','Cuidado de Personas',
    'Delivery','Deposito','Diseño','Electricidad','Estética','Gomero','Informática','Jardinería','Limpieza','Liquidador de Sueldos',
    'Mantenimiento','Marketing','Masoterapeuta','Mecánico','Metalúrgico','Mozo','Operario','Panadería','Peluquería','Pintor','Poda',
    'Recepción','Repositor','Secretariado','Seguridad','Tapicero','Telefonista','Telemarketing','Turismo','Ventas','Viña'];
    const computacion = ['Administración', 'Arquitectura', 'Diseño', 'Contable', 'Programación', 'Stock', 'Ventas','Word', 'Excel',
     'Access','Bejerman', 'Bigsys', 'AFIP', 'Tango Gestión', '3D Studio', 'Autocad', 'Corel Draw', 'Illustrator', 'Photoshop', 'Publisher', 'Otros'];
    const categorias = ['A1', 'A2', 'A3', 'B1', 'B2', 'C1', 'C2','C3', 'D1', 'D2','D3', 'D4', 'E1', 'E2', 'F', 'G1', 'G2','G3'];
    const movilidad = ['Auto','Bicicleta','Camioneta','Moto'];
    const discapacidad =['Auditiva', 'Intelectual', 'Motriz', 'Visceral','Visual', 'Otra'];
    const distrito = ['Agrelo', 'Cacheuta', 'Carrodilla', 'Chacras de Coria', 'Lujan de Cuyo', 'Mayor Drummond', 'El Carrizal', 'La Puntilla', 'Las Compuertas', 'Perdriel', 'Potrerillos', 'Ugarteche', 'Vertientes del Pedemonte', 'Vistalba'];
    const interese = ['Atención al Público', 'Barbería', 'Carpintería', 'Club Empleo Joven', 'Costura/Textil', 'Informática', 'Introducción al Mundo del Trabajo', 'Metalúrgica', 'Panadería', 'Pastelería', 'Peluquería', 'Otro'];
    const titsecundario = ['Bachiller', 'Perito Mercantil/Gestión Administrativa', 'Técnico/aux (otras orientaciones)', 'Técnico/aux Electricista', 'Técnico/aux Electromecánico', 'Técnico/aux Electrónico', 'Técnico/aux en Aeronáutica', 'Técnico/aux en Automotores', 
    'Técnico/aux en Aviónica', 'Técnico/aux en Computación', 'Técnico/aux en Enología', 'Técnico/aux en Industria de Alimentos', 'Técnico/aux en Industria de Proceso', 'Técnico/aux en Informática', 'Técnico/aux en Madera y Muebles', 
    'Técnico/aux en Mecanización Agropecuaria', 'Técnico/aux en Minería', 'Técnico/aux en Producción Agropecuaria', 'Técnico/aux en Programación', 'Técnico/aux Maestro Mayor de Obra','Técnico/aux Mecánico', 'Técnico/aux Óptico', 'Técnico/aux Químico'];
    const horarios=['mañana','tarde','noche','rotativo','franquero'];
    const condiciones=['freelance','teletrabajo','viajante','cama adentro','casero'];

            function optar(opciones,clase){
            const contenedor = document.querySelector(clase);
            const fragmento = document.createDocumentFragment();  
            for (let i = 0; i < opciones.length; i++) {
                const oficio = opciones[i];
                const item = document.createElement("OPTION");
                item.innerHTML=oficio;
                item.setAttribute("value",oficio);
                fragmento.appendChild(item);   
                contenedor.appendChild(fragmento);
                };  
            };
    optar(oficios,".oficios");
    optar(idiomas,".idiomas");
    optar(carreras,".gradoo");
    optar(grado_carr,".grado");
    optar(categorias,".movil_carnet");
    optar(computacion,".computacio");
    optar(movilidad,".movilidad");
    optar(discapacidad,".discapacidad");
    optar(distrito,".distrito");
    optar(titsecundario,".titsecundario");
    optar(interese,".interes_lab");
    optar(carreras,".interes_lab");
    optar(horarios,".disponib_lab");
    optar(condiciones,".condic");

const crearCampo=function(objetivo,clase,arraii){
let classse=clase+(Math.floor(Math.random()*1000));
let cont = document.createDocumentFragment(); 
let conten=document.getElementById(objetivo);  
let newDiv = document.createElement("DIV");
newDiv.setAttribute("id","poliglota1");
newDiv.setAttribute("class","change_col col-md-6 form-group");

let newLeb = document.createElement("LABEL");
newLeb.setAttribute("for",classse);
newLeb.innerHTML="idiomas";
newLeb.setAttribute("class","control-label col-sm-3")

let newDiv2 = document.createElement("DIV");
newDiv2.setAttribute("class","dropdown  form-control bs3"); //este es el que caga tooo  ***  bootstrap-select

let newSelect = document.createElement("SELECT");
newSelect.setAttribute("name",classse);
newSelect.setAttribute("class","form-select "+classse+" "+clase);
newSelect.setAttribute("title","--idioma--");
newSelect.setAttribute("placeholder","--idioma--");
newSelect.setAttribute("data-live-search","true");
newSelect.setAttribute("aria-label","Default select example");
newSelect.setAttribute("tabindex","null");

let newOpcion = document.createElement("OPTION");
newOpcion.setAttribute("class","bs-title-option");
newSelect.appendChild(newOpcion);
newDiv2.appendChild(newSelect);

let newDiv4 = document.createElement("DIV");
newDiv4.setAttribute("class","col-sm-9 ");
newDiv4.appendChild(newDiv2);

let newLeb2 = document.createElement("LABEL");
newLeb2.setAttribute("for","idiomaN");
newLeb2.innerHTML="Nivel";
newLeb2.setAttribute("class","control-label col-sm-3");

let newDiv3 = document.createElement("DIV");
newDiv3.setAttribute("class","col-sm-9");

let newInput= document.createElement("INPUT");
newInput.setAttribute("name","idiomaN");
newInput.setAttribute("type","number");
newInput.setAttribute("maxlength","1");
newInput.setAttribute("class","form-control N"+clase);
newDiv3.appendChild(newInput);

let newBoton = document.createElement("BUTTON");
newBoton.setAttribute("id","removeRow");
newBoton.setAttribute("class","btn btn-danger");
newBoton.innerHTML="borrar";
newDiv3.appendChild(newBoton);

newDiv.appendChild(newLeb);
newDiv.appendChild(newDiv4);
newDiv.appendChild(newLeb2);
newDiv.appendChild(newDiv3);

cont.appendChild(newDiv);
conten.appendChild(cont);

optar(arraii,("."+classse));
};
// borrar registro
$(document).on('click', '#removeRow', function () {
$(this).closest('#poliglota1').remove();
});

let botonIdiom=document.getElementById("addCampo");
botonIdiom.addEventListener("click",functi=>crearCampo("poliglota","idioma",idiomas));

let botonCompu=document.getElementById("addCampoCompu");
botonCompu.addEventListener("click",functi=>crearCampo("tecno","computacio",computacion));

</script>


