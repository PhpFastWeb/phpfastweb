<?php
	$tiempo = explode(" ",microtime()); 
	$tiempoInicio = $tiempo[1] + $tiempo[0]; 
	//--------------------------------------------------
	require_once('../require_once_all_class.inc.php');
	if(!isset($_SESSION)) @session_start();
?><html>
<head>
	<style type="text/css">
	* { font-family: sans-serif;  }
	body { margin: 0 0 0 0; padding: 3px 3px 3px 3px; font-size: 11px;  }
	h1 { font-size: 15px; margin-bottom:2px;}
	h2 { font-size: 14px; }
	h3 { font-size: 12px; }
	</style>
	<title>Ejecución de tareas</title>
</head>
<body>
<?php
	//Comprobamos que se han pasado correctamente los parámetros
	$ok['id'] = ! empty( $_GET['id'] );
	$ok['lista_en_sesion'] = $ok['id'] && ! empty( $_SESSION['tasks_lists'][$_GET['id']] );
	$ok_all = true;
	foreach ($ok as $key => $value ) { $ok_all = $ok_all && $value; }
	
	if ( ! $ok_all ) {
		?>
		<h1>No se encontró la lista de tareas especificadas</h1>
		<p> Nombre de la lista: <?php echo $ok['nombre_lista'];?><br />
			Lista en sesión: <?php echo $ok['lista_en_sesion'];?></p>
		<?php
	} else {
		if ( empty($_GET['command'])) {		
			?>	
			<h1><img src="../themes/executive/img/processing.gif" alt="ejecutando, espere..." align="middle" style="float:right;" /> Ejecutando tareas...</h1>
			<iframe src="<?php echo $_SERVER['PHP_SELF'];?>?command=execute&id=<?php echo $_GET['id'];?>"
					style="width: 398px; height:260px;">
			</iframe><br />
			<b>No cierre esta ventana o se interrumpirán los procesos en ejecución.</b>
			<?php 
		} else {
			$id = $_GET['id'];
			switch($_GET['command']) {
				case 'show':
				default:
					echo "Lista:<br />";
					$tareas_actualizar = $_SESSION['tasks_lists'][$id];
					//Volcamos la lista de resultados al navegador:
					echo $tareas_actualizar->get_list_details();					
				break;
				//---------------------------------------------
				case 'execute':	
				//---------------------------------------------
					//echo "Ejecución:<br />";
					$tareas_actualizar = $_SESSION['tasks_lists'][$id];
					//Volcamos las estadísticas actuales
					echo $tareas_actualizar->get_status_resume();
					echo "<br />\r\n";
					if ( ! isset( $_GET['index'] ) ) {
						$index = $tareas_actualizar->get_first_index();
					} else {
						$index = $_GET['index'];
						//Ejecutamos tareas:
						$tiempoFin = explode(' ',microtime()); $tiempoFin = $tiempoFin[1] + $tiempoFin[0];
						$tiempoReal = ($tiempoFin - $tiempoInicio);
						while( $tiempoReal < 30 && $tareas_actualizar->has_undone_tasks() ) {
							$tareas_actualizar->execute_index($index);
							$index++;
							$tiempoFin = explode(" ",microtime()); $tiempoFin = $tiempoFin[1] + $tiempoFin[0];
							$tiempoReal = ($tiempoFin - $tiempoInicio);
						}
						//Volcamos las tareas realizadas
						echo $tareas_actualizar->get_list_completed();
					}
					if ($tareas_actualizar->has_undone_tasks()) {
						$link = $_SERVER['PHP_SELF']."?command=execute&id=$id&index=$index";
						//echo $link;
						//echo "Si no es automáticamente redirigido a la siguiente página, ";
						//echo "<a href=\"$link\">pulse aquí</a>";
						echo "<script type=\"text/javascript\">\r\n";
						echo "document.location=\"$link\";\r\n";
						echo "</script>\r\n";						
					} else {
						$link = $_SERVER['PHP_SELF']."?command=done&id=$id&index=$index";
						echo "<script type=\"text/javascript\">\r\n";
						echo "window.frames.parent.document.location=\"$link\";\r\n";
						echo "</script>\r\n";						
					}
					
				break;
				//---------------------------------------------
				case 'done':
					echo "<h1>Completado</h1>\r\n";
					$tareas_actualizar = $_SESSION['tasks_lists'][$id];
					echo $tareas_actualizar->get_status_resume();
					echo '<br />';
					
					echo '<div class="s5_foot_info">';
					echo $tareas_actualizar->get_list_completed();
					echo '</div>';
					echo '<a href="#" onclick="window.close();">Cerrar Ventana</a>';
				break;
				case 'resume':
					?>
						<h1>Ejecución finalizada</h1>
						<b>Resultado:</b><br />
					<?php
					echo $tareas_actualizar->get_results();
				break;
			}
		}
	}
	?>
	
</body>
</html>