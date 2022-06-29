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
<div class="row" id="row-persona">
					<h2 class="text-center">Datos personales</h2>
					<?php foreach ($field as $field): ?>
						<div class="change_col col-md-6 form-group">
							<?php echo $field['label']; ?> 
							<?php echo $field['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
                   
                   
                    <div class="row" id="row-domicilio">
					<br />
					<h2 class="text-center form-group">Datos domilicio</h2>
					<?php foreach ($fields_domicilio as $field_domicilio): ?>
						<div class="change_col col-md-6 form-group">
							<?php echo $field_domicilio['label']; ?> 
							<?php echo $field_domicilio['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
                    </div>

                <div class="ln_solid"></div>
                <div class="row">
                    <div class="border-group form-gruop">
                    <div class="change_col col-md-6 form-group">
                        <div class="dropdown bootstrap-checkbox form-control bs3">
                                <?php echo $fields['capacitacion']['label']; ?> 
                                <input type='checkbox' name="capacitacion" class="selectpicker" id="capacitacion" value="s">
                        </div>
                    </div>

                    <div class="change_col col-md-6 form-group">
                        <label for="horAArio_cap" class="control-label col-sm-3">Horario disponible</label> 
                        <div class="col-sm-9">
                        <div class="dropdown bootstrap-select form-control bs3">
                            <select name="horAArio_cap" class="form-control selectpicker horAArio_cap" id="horAArio_cap" data-selected-text-format="count>5" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                            <?php echo $fields['horario_cap']['form']; ?> 
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
                                <label for="interes_lAAb" class="control-label col-sm-3">Intereses laborales</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="interes_lAAb" class="form-control selectpicker interes_lAAb" id="interes_lAAb" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                    <?php echo $fields['interes_lab']['form']; ?> 
                                    </select>
                                </div> 
                            </div>
                    </div>
                    <div class="change_col col-md-6 form-group">
                                <label for="disponib_lAAb" class="control-label col-sm-3">Disponibilidad horaria</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="disponib_lAAb" class="form-control selectpicker disponib_lAAb" id="disponib_lAAb" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                    <?php echo $fields['disponib_lab']['form']; ?> 
                                    </select>
                                </div> 
                            </div>
                    </div>

                    <div class="change_col col-md-6 form-group">
                                <label for="condicAA " class="control-label col-sm-3">condiciones especiales de trabajo</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="condicAA " class="form-control selectpicker condicAA " id="condicAA " data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                    <?php echo $fields['condic']['form']; ?> 
                                    </select>
                                </div> 
                            </div>
                    </div>
                 </div>

                <div class="ln_solid"></div>
                <div class="row">
                    <div class="border-group form-gruop">
                    <div class="change_col col-md-6 form-group">
                                <label for="movilidAAd" class="control-label col-sm-3">Vehiculo propio</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="movilidAAd" class="form-control selectpicker movilidAAd" id="movilidAAd" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                    <?php echo $fields['movilidad']['form']; ?> 
                                    </select>
                                </div> 
                            </div>
                    </div>
                 
                    <div class="change_col col-md-6 form-group">
                                <label for="movil_cAArnet" class="control-label col-sm-3">Carnet de conducir</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="movil_cAArnet" class="form-control selectpicker movil_cAArnet" id="movil_cAArnet" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                    <?php echo $fields['movil_carnet']['form']; ?> 
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
                                <label for="discapAAcidad" class="control-label col-sm-3">Tipo de discapacidad</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="discapAAcidad" class="form-control selectpicker discapAAcidad" id="discapAAcidad" data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                    <?php echo $fields['discapacidad']['form']; ?> 
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
                                <label for="estudiosOtAA " class="control-label col-sm-3">Titulo secundario</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="estudiosOtAA " class="form-control selectpicker titsecundario" id="estudiosOtAA " data-live-search="true" title="-- Seleccionar --" tabindex="null" >
                                    <?php echo $fields['estudiosOt']['form']; ?> 
                                    </select>
                                </div> 
                            </div>
                    </div>

                    <div class="change_col col-md-6 form-group">
                                <label for="grAAdo" class="control-label col-sm-3">Titulo de grado</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="grAAdo" class="form-control selectpicker grAAdo" id="grAAdo" data-live-search="true" title="-- Seleccionar --" tabindex="null" >
                                    <?php echo $fields['grado']['form']; ?> 
                                    </select>
                                </div> 
                            </div>
                    </div>
                    
                    <div class="change_col col-md-6 form-group">
                                <label for="grAAdoo" class="control-label col-sm-3">Rubro</label> 
                                <div class="col-sm-9">
                                    <div class="dropdown bootstrap-select form-control bs3">
                                    <select name="grAAdoo" class="form-control selectpicker grAAdoo" id="grAAdoo" data-live-search="true" title="-- Seleccionar --" tabindex="null" >
                                    <?php echo $fields['gradoo']['form']; ?> 
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
                            <label for="oficiosAA " class="control-label col-sm-3">oficios</label> 
                                <div class="col-sm-9"><div class="dropdown bootstrap-select form-control bs3">
                                    <select name="oficiosAA " class="form-control selectpicker oficiosAA " id="oficiosAA " data-live-search="true" title="-- Seleccionar --" tabindex="null" multiple>
                                    <?php echo $fields['oficios']['form']; ?> 
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
                <div class="text-center envi">
                    <?php echo (!empty($txt_btn)) ? form_submit($data_submit, $txt_btn) : ''; ?>
                    <?php echo ($txt_btn === 'Editar' || $txt_btn === 'Eliminar') ? form_hidden('id', $empleo->cuil) : ''; ?>
                    <a href="oficina_de_empleo/pedir_empleo/listar" class="btn btn-default btn-sm">Cancelar</a> 
                </div>



                <script>  
                    //***esto toma los datos que no se crean bien y los mete en un div del form antes de salir de la pagina para enviar***
                    let idioma=document.querySelectorAll(".idioma");
                    let idiomaN=document.querySelectorAll(".Nidioma");
                    let compu=document.querySelectorAll(".computacio");
                    let compuN=document.querySelectorAll(".Ncomputacio");
                    const otroFragmento = document.createDocumentFragment();
                    const conten=document.querySelector(".otro");

                    let compilador=function(arra1,arra2,nombre){
                        let compilado;
                        for (let index = 0; index < arra1.length; index++) {
                             compilado += (arra1[index]['value']!='undefined')?(arra1[index]['value']+" "+(arra2[index]['value']?arra2[index]['value']:" ")+" , "):"" ;
                             let newInput= document.createElement("INPUT");
                            newInput.setAttribute("name",nombre);
                            newInput.setAttribute("value",compilado);
                            newInput.setAttribute("hidden","true");
                            newInput.setAttribute("id",nombre);
                            otroFragmento.appendChild(newInput);
                    };
                            conten.appendChild(otroFragmento);
                    };
                  
                    let relleno=function(){
                        compilador(idioma,idiomaN,"idiomas");
                        compilador(compu,compuN,"computacion");    
                    };  
               //      window.onbeforeunload = relleno();                   





                //***estas funciones combinan los select para poder ser leidos por el controlador***
                const LisHorarioCap = document.getElementById('horAArio_cap');
                const LisoficiosAA = document.getElementById("oficiosAA ");
                const LisgrAAdoo = document.getElementById("grAAdoo");
                const LisgrAAdo = document.getElementById("grAAdo");
                const LisCarnet = document.getElementById("movil_cAArnet");
                const LismovilidAAd = document.getElementById("movilidAAd");
                const LisDisc = document.getElementById("discapAAcidad");
                const LisEstOt = document.getElementById("estudiosOtAA ");
                const LisIntLab = document.getElementById("interes_lAAb");
                const LisDispLab = document.getElementById("disponib_lAAb");
                const LiscondicAA = document.getElementById("condicAA ");

                function combinnna(e,elemento){
                    let bueno=document.getElementById(elemento);
                    let bastardo=elemento.replace("AA ","");
                    bastardo=bastardo.replace("AA","a");
                    bastardo=document.getElementById(bastardo);
                    let valo=[...e.target.selectedOptions].map(o => o.value)
                    bastardo.value=valo;
                };

                LisHorarioCap.onchange = (e) => { combinnna(e,'horAArio_cap')};
                LisoficiosAA.onchange = (e) => { combinnna(e,'oficiosAA ')};
                LisgrAAdoo.onchange = (e) => { combinnna(e,'grAAdoo')};
                LisgrAAdo.onchange = (e) => { combinnna(e,'grAAdo')};
                LisCarnet.onchange = (e) => { combinnna(e,'movil_cAArnet')};
                LismovilidAAd.onchange = (e) => { combinnna(e,'movilidAAd')};
                LisDisc.onchange = (e) => { combinnna(e,'discapAAcidad')};
                LisEstOt.onchange = (e) => { combinnna(e,'estudiosOtAA ')};
                LisIntLab.onchange = (e) => { combinnna(e,'interes_lAAb')};
                LisDispLab.onchange = (e) => { combinnna(e,'disponib_lAAb')};
                LiscondicAA.onchange = (e) => { combinnna(e,'condicAA ')};
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

<script>
    const grAAdo_carr = ['Analista','Auxiliar','Doctorado','Ingeniería','Licenciatura','Maestro','Profesorado','Tecnicatura'];
    const carreras = ['Abogacía','Acompañante Terapeutico','Administración','Administración Contable','Agente Sanitario','Agronomía','Anestesista','Arquitectura','Bioimágen','Bioquímica','Bromatología',
    'Ciencias Políticas y Administración Pública','Cine y Video','Civil','Comercio Exterior','Comunicación Digital','Comunicación Digital','Comunicación Social','Contador','Criminología',
    'Diagnóstico por Imágen','Diseño Grafico','Diseño Industrial','Diseño y Animación','Economía','Educación Física','Educación Inicial','Educación Primaria','Educación Secundaria','Educación Terciaria/Universitaria',
    'Electromecánica','Electrónica','Enfermería','Enología','Farmacia','Fonoaudiología','Gastronomía','Geología','Gestión Ambiental','Gestor','Grafología','Guía Turismo','Higiene y Seguridad Laboral',
    'Hotelería y Turismo','Industrial','Instrumentista Quirúrgico','Internacionales','Jardin Maternal','Kinesiología','Laboratorio','Letras','Logística y Transporte','Marketing','Martillero Público, Corredor Inmobiliario',
    'Masoterapia','Mecánica y Producción Automatizada','Medicina','Metalmecánico','Minería y Gas Sustentable','Niñez, Adolescencia y Familia','Nutrición','Obstetricia','Odontología','Otros',
    'Petróleo y Gas','Programador','Protesis Dental','Psicología','Psicopedagogía','Publicidad','Química Industrial','Radiología','Recursos Humanos','Recursos Naturales','Relaciones Humanas',
    'Seguridad Pública y Penitenciaría','Sistemas','Sonido','Trabajo Social'];
    const idiomas = ['Albanés', 'Alemán', 'Arabe', 'Bielorruso', 'Búlgaro', 'Catalán', 'Checo', 'Chino', 'Coreano', 'Croata',
    'Danés', 'Eslovaco', 'Esloveno', 'Español', 'Estonio', 'Frances', 'Filandés', 'Griego', 'Húngaro', 'Idish', 'Indonesio',
    'Indonesio', 'Inglés', 'Islandés', 'Italiano','Japonés', 'Ladino', 'Latín', 'Letón', 'Lituano', 'Neerlandés', 'Noruego',
    'Polaco', 'Portugues', 'Rumano', 'Ruso', 'Serbio', 'Sueco', 'Turco', 'Ucraniano', 'Vasco'];
    const oficiosAA = ['Administración','Atención al Público','Bachero','Barbero','Bodega','Cadete','Cajero','Carpintería','Chapería y Pintura',
    'Chofer','Cosecha','Cocina','Community Manager','Construcción','Control de Stock','Costura/Textil','Cuidado de Personas',
    'Delivery','Deposito','Diseño','Electricidad','Estética','Gomero','Informática','Jardinería','Limpieza','Liquidador de Sueldos',
    'Mantenimiento','Marketing','Masoterapeuta','Mecánico','Metalúrgico','Mozo','Operario','Panadería','Peluquería','Pintor','Poda',
    'Recepción','Repositor','Secretariado','Seguridad','Tapicero','Telefonista','Telemarketing','Turismo','Ventas','Viña'];
    const computacion = ['Administración', 'Arquitectura', 'Diseño', 'Contable', 'Programación', 'Stock', 'Ventas','Word', 'Excel',
     'Access','Bejerman', 'Bigsys', 'AFIP', 'Tango Gestión', '3D Studio', 'Autocad', 'Corel Draw', 'Illustrator', 'Photoshop', 'Publisher', 'Otros'];
    const categorias = ['A1', 'A2', 'A3', 'B1', 'B2', 'C1', 'C2','C3', 'D1', 'D2','D3', 'D4', 'E1', 'E2', 'F', 'G1', 'G2','G3'];
    const movilidAAd = ['Auto','Bicicleta','Camioneta','Moto'];
    const discapAAcidad =['Auditiva', 'Intelectual', 'Motriz', 'Visceral','Visual', 'Otra'];
    const interese = ['Atención al Público', 'Barbería', 'Carpintería', 'Club Empleo Joven', 'Costura/Textil', 'Informática', 'Introducción al Mundo del Trabajo', 'Metalúrgica', 'Panadería', 'Pastelería', 'Peluquería', 'Otro'];
    const titsecundario = ['Bachiller', 'Perito Mercantil/Gestión Administrativa', 'Técnico/aux (otras orientaciones)', 'Técnico/aux Electricista', 'Técnico/aux Electromecánico', 'Técnico/aux Electrónico', 'Técnico/aux en Aeronáutica', 'Técnico/aux en Automotores', 
    'Técnico/aux en Aviónica', 'Técnico/aux en Computación', 'Técnico/aux en Enología', 'Técnico/aux en Industria de Alimentos', 'Técnico/aux en Industria de Proceso', 'Técnico/aux en Informática', 'Técnico/aux en Madera y Muebles', 
    'Técnico/aux en Mecanización Agropecuaria', 'Técnico/aux en Minería', 'Técnico/aux en Producción Agropecuaria', 'Técnico/aux en Programación', 'Técnico/aux Maestro Mayor de Obra','Técnico/aux Mecánico', 'Técnico/aux Óptico', 'Técnico/aux Químico'];
    const horarios = ['mañana','tarde','noche','rotativo','franquero'];
    const condiciones = ['freelance','teletrabajo','viajante','cama adentro','casero'];
    const horCap = ['mañana','tarde','noche'];

    function optar(opciones,clase){
        const contenedor = document.getElementById(clase);
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
    optar(oficiosAA,"oficiosAA ");
    optar(idiomas,"idioma");
    optar(carreras,"grAAdoo");
    optar(grAAdo_carr,"grAAdo");
    optar(categorias,"movil_cAArnet");
    optar(computacion,"computacio");
    optar(movilidAAd,"movilidAAd");
    optar(discapAAcidad,"discapAAcidad");
    optar(titsecundario,"estudiosOtAA ");
    optar(interese,"interes_lAAb");
    optar(carreras,"interes_lAAb");
    optar(horarios,"disponib_lAAb");
    optar(condiciones,"condicAA ");
    optar(horCap,"horAArio_cap");

    const crearCampo=function(objetivo,clase,arraii,nombre){
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
        newSelect.setAttribute("title","--"+nombre+"--");
        newSelect.setAttribute("placeholder","--"+nombre+"--");
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
// borrar campo
    $(document).on('click', '#removeRow', function(){
        $(this).closest('#poliglota1').remove();
    });

    let botonIdiom=document.getElementById("addCampo");
    botonIdiom.addEventListener("click",functi=>crearCampo("poliglota","idioma",idiomas,"idioma"));       

    let botonCompu=document.getElementById("addCampoCompu");
    botonCompu.addEventListener("click",functi=>crearCampo("tecno","computacio",computacion,"programa"));

</script>

<script>
	$(document).ready(function() {
		domicilio_row();
		$('#cuil').inputmask({
			mask: '99-99999999-9',
			removeMaskOnSubmit: true
		});
			$('#carga_domicilio').on('changed.bs.select', function(e) {
				domicilio_row();
			});
			function domicilio_row() {
					$('#row-domicilio :input').attr("disabled", false);
					$("#localidad").selectpicker('refresh');
					$("#row-domicilio").show();
			}
	});
</script>

<script>
                 
//***esto toma los datos que no se cargan bien, elimina el campo input donde se cargan y reescribe los campos select, que ya son creados por ala funcion de mas arriba

    function reemplazar(elemento){              let bueno=document.getElementById(elemento);
        let bastardo=elemento.replace("AA ","");
        bastardo=bastardo.replace("AA","a");
        bastardo=document.getElementById(bastardo);
        let valores=(bastardo.getAttribute('value')!=null)?bastardo.getAttribute('value'):"";
        let valorArr=valores.split(", ");
        if(valores!=""){
            for (let index = 0; index <valorArr.length ; index++) {
                    let primo=bueno.firstElementChild;
                    console.log(bueno.children);
            for (let inde = 0; inde < bueno.children.length; inde++) {
                    if (valorArr[index]==primo.getAttribute('value')) {
                        primo.setAttribute("selected","selected");        
                        primo=primo.nextElementSibling;     
                        break;
                    };
                    (primo==bueno.lastElementChild)?'break':(primo=primo.nextElementSibling);
                };
            };
        };
    }
window.onload = function (){
    reemplazar('horAArio_cap');
    reemplazar("oficiosAA ");
    reemplazar("grAAdoo");
    reemplazar("grAAdo");
    reemplazar("movil_cAArnet");
    reemplazar("movilidAAd");
    reemplazar("discapAAcidad");
    reemplazar("estudiosOtAA ");
    reemplazar("interes_lAAb");
    reemplazar("disponib_lAAb");
    reemplazar("condicAA ");
}
</script>