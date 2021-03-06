<?php
/*
* $Id$
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
	die();
}

//INSERT INTO droits SET id='/mod_abs2/calcul_score.php',administrateur='V',professeur='F',cpe='V',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Exports statistiques',statut='';
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

include_once 'lib/function.php';

$msg="";

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);
$choisir_formule=isset($_POST['choisir_formule']) ? $_POST['choisir_formule'] : (isset($_GET['choisir_formule']) ? $_GET['choisir_formule'] : NULL);

/*
$formule_score_abs=getSettingValue('formule_score_abs');
if($formule_score_abs=="") {
	$formule_score_abs="20-0.5*RET-NBNJ";
}
*/

/*

$formule_score_abs_nb_1=getSettingValue('formule_score_abs_nb_1');

$formule_score_abs_plus_moins_RET=getSettingValue('formule_score_abs_plus_moins_RET');
$formule_score_abs_plus_moins_NBABS=getSettingValue('formule_score_abs_plus_moins_NBABS');
$formule_score_abs_plus_moins_NBNJ=getSettingValue('formule_score_abs_plus_moins_NBNJ');

$formule_score_abs_scalaire_RET=getSettingValue('formule_score_abs_scalaire_RET');
$formule_score_abs_scalaire_NBABS=getSettingValue('formule_score_abs_scalaire_NBABS');
$formule_score_abs_scalaire_NBNJ=getSettingValue('formule_score_abs_scalaire_NBNJ');

$formule_score_abs_puissance_RET=getSettingValue('formule_score_abs_puissance_RET');
$formule_score_abs_puissance_NBABS=getSettingValue('formule_score_abs_puissance_NBABS');
$formule_score_abs_puissance_NBNJ=getSettingValue('formule_score_abs_puissance_NBNJ');

*/

$formule_score_abs_nb_1=isset($_POST['formule_score_abs_nb_1']) ? $_POST['formule_score_abs_nb_1'] : getSettingValue('formule_score_abs_nb_1');
if(($formule_score_abs_nb_1=="")||(!is_numeric($formule_score_abs_nb_1))) {$formule_score_abs_nb_1="20";}

$formule_score_abs_plus_moins_RET=isset($_POST['formule_score_abs_plus_moins_RET']) ? $_POST['formule_score_abs_plus_moins_RET'] : getSettingValue('formule_score_abs_plus_moins_RET');
if(($formule_score_abs_plus_moins_RET!="+")||($formule_score_abs_plus_moins_RET!="-")) {$formule_score_abs_plus_moins_RET="-";}
$formule_score_abs_plus_moins_NBABS=isset($_POST['formule_score_abs_plus_moins_NBABS']) ? $_POST['formule_score_abs_plus_moins_NBABS'] : getSettingValue('formule_score_abs_plus_moins_NBABS');
if(($formule_score_abs_plus_moins_NBABS!="+")||($formule_score_abs_plus_moins_NBABS!="-")) {$formule_score_abs_plus_moins_NBABS="-";}
$formule_score_abs_plus_moins_NBNJ=isset($_POST['formule_score_abs_plus_moins_NBNJ']) ? $_POST['formule_score_abs_plus_moins_NBNJ'] : getSettingValue('formule_score_abs_plus_moins_NBNJ');
if(($formule_score_abs_plus_moins_NBNJ!="+")||($formule_score_abs_plus_moins_NBNJ!="-")) {$formule_score_abs_plus_moins_NBNJ="-";}

$formule_score_abs_scalaire_RET=isset($_POST['formule_score_abs_scalaire_RET']) ? $_POST['formule_score_abs_scalaire_RET'] : getSettingValue('formule_score_abs_scalaire_RET');
if(($formule_score_abs_scalaire_RET=="")||(!is_numeric($formule_score_abs_scalaire_RET))) {$formule_score_abs_scalaire_RET=0.5;}
$formule_score_abs_scalaire_NBABS=isset($_POST['formule_score_abs_scalaire_NBABS']) ? $_POST['formule_score_abs_scalaire_NBABS'] : getSettingValue('formule_score_abs_scalaire_NBABS');
if(($formule_score_abs_scalaire_NBABS=="")||(!is_numeric($formule_score_abs_scalaire_NBABS))) {$formule_score_abs_scalaire_NBABS=0;}
$formule_score_abs_scalaire_NBNJ=isset($_POST['formule_score_abs_scalaire_NBNJ']) ? $_POST['formule_score_abs_scalaire_NBNJ'] : getSettingValue('formule_score_abs_scalaire_NBNJ');
if(($formule_score_abs_scalaire_NBNJ=="")||(!is_numeric($formule_score_abs_scalaire_NBNJ))) {$formule_score_abs_scalaire_NBNJ=1;}

$formule_score_abs_puissance_RET=isset($_POST['formule_score_abs_puissance_RET']) ? $_POST['formule_score_abs_puissance_RET'] : getSettingValue('formule_score_abs_puissance_RET');
if(($formule_score_abs_puissance_RET=="")||(!is_numeric($formule_score_abs_puissance_RET))) {$formule_score_abs_puissance_RET=1;}
$formule_score_abs_puissance_NBABS=isset($_POST['formule_score_abs_puissance_NBABS']) ? $_POST['formule_score_abs_puissance_NBABS'] : getSettingValue('formule_score_abs_puissance_NBABS');
if(($formule_score_abs_puissance_NBABS=="")||(!is_numeric($formule_score_abs_puissance_NBABS))) {$formule_score_abs_puissance_NBABS=1;}
$formule_score_abs_puissance_NBNJ=isset($_POST['formule_score_abs_puissance_NBNJ']) ? $_POST['formule_score_abs_puissance_NBNJ'] : getSettingValue('formule_score_abs_puissance_NBNJ');
if(($formule_score_abs_puissance_NBNJ=="")||(!is_numeric($formule_score_abs_puissance_NBNJ))) {$formule_score_abs_puissance_NBNJ=1;}

if(isset($_POST['enregistrer_formule'])) {
	check_token();

	/*
	$formule_score_abs=$_POST['formule_score_abs'];
	if(($formule_score_abs!="")&&(preg_replace("|[0-9*+/().-]*|","",preg_replace("|RET|","",preg_replace("|NBABS|","",preg_replace("|NBNJ|","",$formule_score_abs))))=="")) {
		if(!saveSetting("$formule_score_abs", $formule_score_abs)) {
			$msg.="Erreur lors de l'enregistrement du paramètre formule_score_abs<br />";
		}
		else {
			$msg.="Enregistrement du paramètre formule_score_abs effectué.<br />";
		}
	}
	else {
		$msg.="La chaine proposée n'est pas valide.";
	}
	*/

	$tab_param=array('formule_score_abs_nb_1',
	'formule_score_abs_plus_moins_RET',
	'formule_score_abs_plus_moins_NBABS',
	'formule_score_abs_plus_moins_NBNJ',
	'formule_score_abs_scalaire_RET',
	'formule_score_abs_scalaire_NBABS',
	'formule_score_abs_scalaire_NBNJ',
	'formule_score_abs_puissance_RET',
	'formule_score_abs_puissance_NBABS',
	'formule_score_abs_puissance_NBNJ');
	for($loop=0;$loop<count($tab_param);$loop++) {
		$param_courant=$tab_param[$loop];
		if(!saveSetting($param_courant, $$param_courant)) {
			$msg.="Erreur lors de l'enregistrement du paramètre $param_courant<br />";
		}
	}
	if($msg=='') {$msg="Enregistrement effectué.";}
}

$formule=$formule_score_abs_nb_1.$formule_score_abs_plus_moins_RET."(".$formule_score_abs_scalaire_RET."*RET<sup title='puissance'>".$formule_score_abs_puissance_RET."</sup>)";
$formule.=$formule_score_abs_plus_moins_NBNJ."(".$formule_score_abs_scalaire_NBNJ."*NBNJ<sup title='puissance'>".$formule_score_abs_puissance_NBNJ."</sup>)";
$formule.=$formule_score_abs_plus_moins_NBABS."(".$formule_score_abs_scalaire_NBABS."*NBABS<sup title='puissance'>".$formule_score_abs_puissance_NBABS."</sup>)";


if(isset($id_classe)) {
	$tab_score=array();
	if(!isset($num_periode)) {
		$sql="SELECT DISTINCT e.nom, e.prenom, e.login FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		$num_ligne=0;
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$eleve = EleveQuery::create()->findOneByLogin($lig_ele->login);

			$tab_score[$num_ligne]["login"]=$lig_ele->login;
			$tab_score[$num_ligne]["prenom_nom"]=$lig_ele->prenom." ".$lig_ele->nom;

			$cpt_per=0;
			foreach($eleve->getPeriodeNotes() as $periode_note) {
				if ($periode_note->getDateDebut() == null) {
					//periode non commencee
					continue;
				}
				$tab_score[$num_ligne]["periodes"][$cpt_per]['nom_periode']=$periode_note->getNomPeriode();
				$tab_score[$num_ligne]["periodes"][$cpt_per]['du']=$periode_note->getDateDebut('d/m/Y');
				if ($periode_note->getDateFin() == null) {
					$tab_score[$num_ligne]["periodes"][$cpt_per]['au']='(non précisé)';
				} else {
					$tab_score[$num_ligne]["periodes"][$cpt_per]['au']=$periode_note->getDateFin('d/m/Y');
				}

				$nb_abs=$eleve->getDemiJourneesAbsenceParPeriode($periode_note)->count();
				$nb_nj=$eleve->getDemiJourneesNonJustifieesAbsenceParPeriode($periode_note)->count();
				$nb_ret=$eleve->getRetardsParPeriode($periode_note)->count();
				$tab_score[$num_ligne]["periodes"][$cpt_per]['nb_abs']=$nb_abs;
				$tab_score[$num_ligne]["periodes"][$cpt_per]['nb_nj']=$nb_nj;
				$tab_score[$num_ligne]["periodes"][$cpt_per]['nb_ret']=$nb_ret;

				$chaine=$formule_score_abs_nb_1;
				$chaine_title=$formule_score_abs_nb_1;
				if($formule_score_abs_plus_moins_RET=="+") {
					$chaine+=$formule_score_abs_scalaire_RET*pow($nb_ret,$formule_score_abs_puissance_RET);
					$chaine_title.=" + (".$formule_score_abs_scalaire_RET." * ".$nb_ret."^".$formule_score_abs_puissance_RET.")";
				}
				else {
					$chaine-=$formule_score_abs_scalaire_RET*pow($nb_ret,$formule_score_abs_puissance_RET);
					$chaine_title.=" - (".$formule_score_abs_scalaire_RET." * ".$nb_ret."^".$formule_score_abs_puissance_RET.")";
				}

				if($formule_score_abs_plus_moins_NBNJ=="+") {
					$chaine+=$formule_score_abs_scalaire_NBNJ*pow($nb_nj,$formule_score_abs_puissance_NBNJ);
					$chaine_title.=" + (".$formule_score_abs_scalaire_NBNJ." * ".$nb_nj."^".$formule_score_abs_puissance_NBNJ.")";
				}
				else {
					$chaine-=$formule_score_abs_scalaire_NBNJ*pow($nb_nj,$formule_score_abs_puissance_NBNJ);
					$chaine_title.=" - (".$formule_score_abs_scalaire_NBNJ." * ".$nb_nj."^".$formule_score_abs_puissance_NBNJ.")";
				}

				if($formule_score_abs_plus_moins_NBABS=="+") {
					$chaine+=$formule_score_abs_scalaire_NBABS*pow($nb_abs,$formule_score_abs_puissance_NBABS);
					$chaine_title.=" + (".$formule_score_abs_scalaire_NBABS." * ".$nb_abs."^".$formule_score_abs_puissance_NBABS.")";
				}
				else {
					$chaine-=$formule_score_abs_scalaire_NBABS*pow($nb_abs,$formule_score_abs_puissance_NBABS);
					$chaine_title.=" - (".$formule_score_abs_scalaire_NBABS." * ".$nb_abs."^".$formule_score_abs_puissance_NBABS.")";
				}

				$tab_score[$num_ligne]["periodes"][$cpt_per]['chaine_title']=$chaine_title;
				$tab_score[$num_ligne]["periodes"][$cpt_per]['chaine']=$chaine;

				$cpt_per++;
			}
			$num_ligne++;
		}
	}
	else {
		$sql="SELECT DISTINCT e.nom, e.prenom, e.login FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.periode='$num_periode' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		$num_ligne=0;
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$eleve = EleveQuery::create()->findOneByLogin($lig_ele->login);
			foreach($eleve->getPeriodeNotes() as $periode_note) {
				if ($periode_note->getDateDebut() == null) {
					//periode non commencee
					continue;
				}
				/*
				echo "<pre>";
				print_r($periode_note);
				echo "</pre>";
				*/
				if($periode_note->getNumPeriode()==$num_periode) {

					$tab_score['eleves'][$num_ligne]["login"]=$lig_ele->login;
					$tab_score['eleves'][$num_ligne]["prenom_nom"]=$lig_ele->prenom." ".$lig_ele->nom;

					if($num_ligne==0) {
						$tab_score['nom_periode']=$periode_note->getNomPeriode();
						$tab_score['du']=$periode_note->getDateDebut('d/m/Y');
						if ($periode_note->getDateFin() == null) {
							$tab_score['au']='(non précisé)';
						} else {
							$tab_score['au']=$periode_note->getDateFin('d/m/Y');
						}
					}

					$nb_abs=$eleve->getDemiJourneesAbsenceParPeriode($periode_note)->count();
					$nb_nj=$eleve->getDemiJourneesNonJustifieesAbsenceParPeriode($periode_note)->count();
					$nb_ret=$eleve->getRetardsParPeriode($periode_note)->count();

					$tab_score["eleves"][$num_ligne]['nb_abs']=$nb_abs;
					$tab_score["eleves"][$num_ligne]['nb_nj']=$nb_nj;
					$tab_score["eleves"][$num_ligne]['nb_ret']=$nb_ret;

					$chaine=$formule_score_abs_nb_1;
					$chaine_title=$formule_score_abs_nb_1;
					if($formule_score_abs_plus_moins_RET=="+") {
						$chaine+=$formule_score_abs_scalaire_RET*pow($nb_ret,$formule_score_abs_puissance_RET);
						$chaine_title.=" + (".$formule_score_abs_scalaire_RET." * ".$nb_ret."^".$formule_score_abs_puissance_RET.")";
					}
					else {
						$chaine-=$formule_score_abs_scalaire_RET*pow($nb_ret,$formule_score_abs_puissance_RET);
						$chaine_title.=" - (".$formule_score_abs_scalaire_RET." * ".$nb_ret."^".$formule_score_abs_puissance_RET.")";
					}

					if($formule_score_abs_plus_moins_NBNJ=="+") {
						$chaine+=$formule_score_abs_scalaire_NBNJ*pow($nb_nj,$formule_score_abs_puissance_NBNJ);
						$chaine_title.=" + (".$formule_score_abs_scalaire_NBNJ." * ".$nb_nj."^".$formule_score_abs_puissance_NBNJ.")";
					}
					else {
						$chaine-=$formule_score_abs_scalaire_NBNJ*pow($nb_nj,$formule_score_abs_puissance_NBNJ);
						$chaine_title.=" - (".$formule_score_abs_scalaire_NBNJ." * ".$nb_nj."^".$formule_score_abs_puissance_NBNJ.")";
					}

					if($formule_score_abs_plus_moins_NBABS=="+") {
						$chaine+=$formule_score_abs_scalaire_NBABS*pow($nb_abs,$formule_score_abs_puissance_NBABS);
						$chaine_title.=" + (".$formule_score_abs_scalaire_NBABS." * ".$nb_abs."^".$formule_score_abs_puissance_NBABS.")";
					}
					else {
						$chaine-=$formule_score_abs_scalaire_NBABS*pow($nb_abs,$formule_score_abs_puissance_NBABS);
						$chaine_title.=" - (".$formule_score_abs_scalaire_NBABS." * ".$nb_abs."^".$formule_score_abs_puissance_NBABS.")";
					}

					$tab_score["eleves"][$num_ligne]['chaine_title']=$chaine_title;
					$tab_score["eleves"][$num_ligne]['chaine']=$chaine;
				}
			}
			$num_ligne++;
		}
	}
}

if((isset($id_classe))&&(isset($_GET['export_csv']))) {
	check_token();

	$csv="";
	$fin_de_ligne="\r\n";
	if(isset($num_periode)) {
		$csv.="LOGIN;PRENOM_NOM;NB_ABS;NB_NJ;NB_RET;SCORE;".$fin_de_ligne;

		for($loop=0;$loop<count($tab_score['eleves']);$loop++) {
			$csv.=$tab_score['eleves'][$loop]["login"].";".$tab_score['eleves'][$loop]["prenom_nom"].";".$tab_score["eleves"][$loop]['nb_abs'].";".$tab_score["eleves"][$loop]['nb_nj'].";".$tab_score["eleves"][$loop]['nb_ret'].";".$tab_score["eleves"][$loop]['chaine'].";".$fin_de_ligne;
		}

		$nom_fic = "score_absences_".remplace_accents(get_nom_classe($id_classe), "all")."_periode_".$num_periode."_".strftime("%Y%m%d_%H%M%S").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);

		echo echo_csv_encoded($csv);
		die();
	}
}

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";

$javascript_specifique[] = "lib/tablekit";
//$dojo=true;
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
$titre_page = "Calcul score";
require_once("../lib/header.inc.php");
//**************** EN-TETE *****************
include('menu_abs2.inc.php');
include('menu_bilans.inc.php');

?>
<div id="contain_div" class="css-panes">

<?php

if(isset($choisir_formule)) {
echo "<p><a href='".$_SERVER['PHP_SELF']."'>Choisir une classe</a></p>\n";

/*
	echo "<form id='choix_formule' name='choix_formule' action='".$_SERVER['PHP_SELF']."' method='post'>
	".add_token_field()."
<p>Vous pouvez saisir une formule faisant référence&nbsp;:</p>
<ul>
	<li><strong>NBABS</strong>&nbsp;: Le nombre d'absences</li>
	<li><strong>NBNJ</strong>&nbsp;: Le nombre d'absences non justifiées</li>
	<li><strong>RET</strong>&nbsp;: Le nombre de retards</li>
</ul>
<p>Formule&nbsp;: <input type='text' name='formule_score_abs' value=\"".$formule_score_abs."\" /> 
<input type='hidden' name='enregistrer_formule' value=\"y\" />
<input type='submit' value='Enregistrer' /></p>
</form>\n";
*/

	echo "<form id='choix_formule' name='choix_formule' action='".$_SERVER['PHP_SELF']."' method='post'>
	".add_token_field()."
<h2>Scores</h2>
<h3>Saisie d'une formule</h3>
<div style='margin-left:3em;'>
<p>Vous pouvez compléter la formule dans laquelle&nbsp;:</p>
<ul>
	<li><strong>NBABS</strong>&nbsp;: Le nombre d'absences</li>
	<li><strong>NBNJ</strong>&nbsp;: Le nombre d'absences non justifiées</li>
	<li><strong>RET</strong>&nbsp;: Le nombre de retards</li>
</ul>
<p><br /></p>
<p>La formule actuelle est&nbsp;: $formule</p>
<p><br /></p>
<p class='bold'>Nouvelle formule&nbsp;:</p>
<table class='boireaus'>
	<tr>
		<td class='lig1'><input type='text' name='formule_score_abs_nb_1' value='$formule_score_abs_nb_1' size='2' /></td>
		<td class='lig-1'>
			<input type='radio' name='formule_score_abs_plus_moins_RET' id='formule_score_abs_plus_moins_RET_plus' value='+' ".($formule_score_abs_plus_moins_RET=="+" ? "checked " : "")."/><label for='formule_score_abs_plus_moins_RET_plus'>+</label><br />
			<input type='radio' name='formule_score_abs_plus_moins_RET' id='formule_score_abs_plus_moins_RET_moins' value='-' ".($formule_score_abs_plus_moins_RET=="-" ? "checked " : "")."/><label for='formule_score_abs_plus_moins_RET_moins'>-</label>
		</td>
		<td class='lig-1'><input type='text' name='formule_score_abs_scalaire_RET' value='$formule_score_abs_scalaire_RET' size='2' /></td>
		<td class='lig-1'>*RET</td>
		<td class='lig-1' title='puissance'>^<input type='text' name='formule_score_abs_puissance_RET' value='$formule_score_abs_puissance_RET' size='2' /></td>

		<td class='lig1'>
			<input type='radio' name='formule_score_abs_plus_moins_NBNJ' id='formule_score_abs_plus_moins_NBNJ_plus' value='+' ".($formule_score_abs_plus_moins_NBNJ=="+" ? "checked " : "")."/><label for='formule_score_abs_plus_moins_NBNJ_plus'>+</label><br />
			<input type='radio' name='formule_score_abs_plus_moins_NBNJ' id='formule_score_abs_plus_moins_NBNJ_moins' value='-' ".($formule_score_abs_plus_moins_NBNJ=="-" ? "checked " : "")."/><label for='formule_score_abs_plus_moins_NBNJ_moins'>-</label>
		</td>
		<td class='lig1'><input type='text' name='formule_score_abs_scalaire_NBNJ' value='$formule_score_abs_scalaire_NBNJ' size='2' /></td>
		<td class='lig1'>*NBNJ</td>
		<td class='lig1' title='puissance'>^<input type='text' name='formule_score_abs_puissance_NBNJ' value='$formule_score_abs_puissance_NBNJ' size='2' /></td>

		<td class='lig-1'>
			<input type='radio' name='formule_score_abs_plus_moins_NBABS' id='formule_score_abs_plus_moins_NBABS_plus' value='+' ".($formule_score_abs_plus_moins_NBABS=="+" ? "checked " : "")."/><label for='formule_score_abs_plus_moins_NBABS_plus'>+</label><br />
			<input type='radio' name='formule_score_abs_plus_moins_NBABS' id='formule_score_abs_plus_moins_NBABS_moins' value='-' ".($formule_score_abs_plus_moins_NBABS=="-" ? "checked " : "")."/><label for='formule_score_abs_plus_moins_NBABS_moins'>-</label>
		</td>
		<td class='lig-1'><input type='text' name='formule_score_abs_scalaire_NBABS' value='$formule_score_abs_scalaire_NBABS' size='2' /></td>
		<td class='lig-1'>*NBABS</td>
		<td class='lig-1' title='puissance'>^<input type='text' name='formule_score_abs_puissance_NBABS' value='$formule_score_abs_puissance_NBABS' size='2' /></td>

	</tr>
</table>

<input type='hidden' name='enregistrer_formule' value=\"y\" />
<input type='submit' value='Enregistrer' /></p>
</div>
</form>\n";

	echo "</div>\n";
	require_once("../lib/footer.inc.php");
	die();
}

//=====================================================================

$tab_classe=array();
$chaine_opt_classes="";
$sql="SELECT DISTINCT id, classe FROM classes c, periodes p WHERE c.id=p.id_classe ORDER BY classe;";
$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig_clas=mysqli_fetch_object($res_clas)) {
	$tab_classe[$lig_clas->id]=$lig_clas->classe;

	$chaine_opt_classes.="<option value='$lig_clas->id'";
	if((isset($id_classe))&&($lig_clas->id==$id_classe)) {
		$chaine_opt_classes.=" selected";
	}
	$chaine_opt_classes.=">$lig_clas->classe</option>\n";
}

//=====================================================================

// Choix de la classe et de la période
if(!isset($id_classe)) {
	echo "<p><a href='".$_SERVER['PHP_SELF']."?choisir_formule=y'>Saisir une formule</a></p>

<h2>Scores</h2>

<p>Pour quelle classe souhaitez-vous souhaitez-vous calculer des scores d'absences/retards des élèves&nbsp;?<br />\n";

	foreach($tab_classe as $current_id_classe => $current_nom_classe) {
		echo "<p><strong>$current_nom_classe</strong>&nbsp;: ";
		$sql="SELECT * FROM periodes WHERE id_classe='$current_id_classe' ORDER BY num_periode;";
		$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
		$cpt_per=0;
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$current_id_classe'>Toutes les périodes</a>";
		$cpt_per++;
		while($lig_per=mysqli_fetch_object($res_per)) {
			if($cpt_per>0) {echo " - ";}
			echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$current_id_classe&amp;num_periode=$lig_per->num_periode'>".$lig_per->nom_periode."</a>";
			$cpt_per++;
		}
		echo "</p>\n";
	}

	echo "</div>\n";
	require_once("../lib/footer.inc.php");
	die();
}

//=====================================================================

require("../lib/periodes.inc.php");

echo "<form id='choix_autre_classe' name='choix_autre_classe' action='".$_SERVER['PHP_SELF']."' method='post'>
	".add_token_field()."
	<p><a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe/période</a> | 
	<select name='id_classe' onchange=\"document.getElementById('choix_autre_classe').submit()\">
		$chaine_opt_classes
	</select><input type='submit' id='choix_autre_classe_submit' value='OK' /> | 
	<a href='".$_SERVER['PHP_SELF']."?choisir_formule=y'>Saisir une formule</a>
	".((isset($num_periode)) ? "<input type='hidden' name='num_periode' value='$num_periode' />" : "")."
	</p>
</form>

<script type='text/javascript'>
	document.getElementById('choix_autre_classe_submit').style.display='none';
</script>

<h2>Calcul des scores</h2>

<p>La formule utilisée est&nbsp;: $formule</p>\n";

if(!isset($num_periode)) {
	for($loop=0;$loop<count($tab_score);$loop++) {

		echo "<table class='boireaus'>\n";
		echo "<caption>Bilan des absences de <strong>".$tab_score[$loop]["prenom_nom"]."</strong></caption>\n";
		echo "<tr>\n";
		echo "<th title=\"Les dates de fin de période correspondent à ce qui est paramétré en colonne 'Date de fin' de la page de Verrouillage des périodes de notes (page accessible en compte scolarité).\">Période</th>\n";
		echo "<th>Nombre d'absences<br/>(1/2 journées)</th>\n";
		echo "<th>Absences non justifiées</th>\n";
		echo "<th>Nombre de retards</th>\n";
		//echo "<th>Appréciation</th>\n";
		echo "<th>Score</th>\n";
		echo "</tr>\n";
		$alt=1;
		for($loop_per=0;$loop_per<count($tab_score[$loop]["periodes"]);$loop_per++) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>".$tab_score[$loop]["periodes"][$loop_per]['nom_periode'];
			echo " du ".$tab_score[$loop]["periodes"][$loop_per]['du'];
			echo " au ".$tab_score[$loop]["periodes"][$loop_per]['au'];
			echo "</td>\n";

			echo "<td";
			if($formule_score_abs_scalaire_NBABS==0) {echo " style='background-color:grey' title='Ne compte pas dans le score calculé.'";}
			echo ">";
			echo $tab_score[$loop]["periodes"][$loop_per]['nb_abs'];
			echo "</td>\n";
			echo "<td";
			if($formule_score_abs_scalaire_NBNJ==0) {echo " style='background-color:grey' title='Ne compte pas dans le score calculé.'";}
			echo ">";
			echo $tab_score[$loop]["periodes"][$loop_per]['nb_nj'];
			echo "</td>\n";
			echo "<td";
			if($formule_score_abs_scalaire_RET==0) {echo " style='background-color:grey' title='Ne compte pas dans le score calculé.'";}
			echo ">";
			echo $tab_score[$loop]["periodes"][$loop_per]['nb_ret'];
			echo "</td>\n";
			/*
			echo "<td>";
			// PROBLEME: On n'a plus accès à cette table si on ne remplit pas la table absences.
			//           Revoir la façon dont on remplit l'appréciation, peut-être donner l'accès à la page absences/saisie_absences.php
			//           sans permettre la modif des retards/abs/nj)
			$sql="SELECT * FROM absences WHERE (login='".$lig_ele->login."' AND periode='".$periode_note->getNumPeriode()."');";
			$current_eleve_absences_query = mysql_query($sql);
			$current_eleve_appreciation_absences = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
			echo $current_eleve_appreciation_absences;
			echo "</td>\n";
			*/

			echo "<td title=\"".$tab_score[$loop]["periodes"][$loop_per]['chaine_title']." = ".$tab_score[$loop]["periodes"][$loop_per]['chaine']."\">";
			echo $tab_score[$loop]["periodes"][$loop_per]['chaine'];
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
}
else {
	if($num_periode==1) {
		$info_dates_per=" (<em title=\"Les dates de fin de période correspondent à ce qui est paramétré en colonne 'Date de fin' de la page de Verrouillage des périodes de notes (page accessible en compte scolarité).\">du début de l'année jusqu'au ".formate_date($date_fin_periode[$num_periode])."</em>)";
	}
	else {
		$info_dates_per=" (<em title=\"Les dates de fin de période correspondent à ce qui est paramétré en colonne 'Date de fin' de la page de Verrouillage des périodes de notes (page accessible en compte scolarité).\">du ".formate_date($date_fin_periode[$num_periode-1])." au ".formate_date($date_fin_periode[$num_periode])."</em>)";
	}

	echo "<div id='div_lien_csv' style='float:right; width:16px; margin:0.5em; display:none;'><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;num_periode=$num_periode&amp;export_csv=y".add_token_in_url()."' title='Exporter ces scores en CSV'><img src='../images/icons/csv.png' class='icone16' alt='CSV' /></a></div>";
	echo "<table class='sortable resizable boireaus'>\n";
	echo "<caption><strong>Bilan des absences en période $num_periode</strong>".$info_dates_per."</caption>\n";
	echo "<tr>\n";
	echo "<th class='text' title=\"Les dates de fin de période correspondent à ce qui est paramétré en colonne 'Date de fin' de la page de Verrouillage des périodes de notes (page accessible en compte scolarité).\">Période</th>\n";
	echo "<th class='number' title='Cliquez pour trier'>Nombre d'absences<br/>(1/2 journées)</th>\n";
	echo "<th class='number' title='Cliquez pour trier'>Absences non justifiées</th>\n";
	echo "<th class='number' title='Cliquez pour trier'>Nombre de retards</th>\n";
	//echo "<th>Appréciation</th>\n";
	echo "<th class='number' title='Cliquez pour trier'>Score</th>\n";
	echo "</tr>\n";

	$alt=1;
	//$tab_score['eleves'][$num_ligne]["prenom_nom"]
	for($loop=0;$loop<count($tab_score['eleves']);$loop++) {
		//$tab_score['nom_periode']
		//$tab_score["eleves"][$num_ligne]['nb_abs']

		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td title=\"".$tab_score['nom_periode'];
		echo " du ".$tab_score['du'];
		echo " au ".$tab_score['au'];
		echo "\">";
		echo $tab_score['eleves'][$loop]["prenom_nom"];
		echo "</td>\n";
		echo "<td";
		if($formule_score_abs_scalaire_NBABS==0) {echo " style='background-color:grey' title='Ne compte pas dans le score calculé.'";}
		echo ">";
		echo $tab_score["eleves"][$loop]['nb_abs'];
		echo "</td>\n";
		echo "<td";
		if($formule_score_abs_scalaire_NBNJ==0) {echo " style='background-color:grey' title='Ne compte pas dans le score calculé.'";}
		echo ">";
		echo $tab_score["eleves"][$loop]['nb_nj'];
		echo "</td>\n";
		echo "<td";
		if($formule_score_abs_scalaire_RET==0) {echo " style='background-color:grey' title='Ne compte pas dans le score calculé.'";}
		echo ">";
		echo $tab_score["eleves"][$loop]['nb_ret'];
		echo "</td>\n";
		/*
		echo "<td>";
		// PROBLEME: On n'a plus accès à cette table si on ne remplit pas la table absences.
		//           Revoir la façon dont on remplit l'appréciation, peut-être donner l'accès à la page absences/saisie_absences.php
		//           sans permettre la modif des retards/abs/nj)
		$sql="SELECT * FROM absences WHERE (login='".$lig_ele->login."' AND periode='".$periode_note->getNumPeriode()."');";
		$current_eleve_absences_query = mysql_query($sql);
		$current_eleve_appreciation_absences = @old_mysql_result($current_eleve_absences_query, 0, "appreciation");
		echo $current_eleve_appreciation_absences;
		echo "</td>\n";
		*/

		echo "<td title=\"".$tab_score["eleves"][$loop]['chaine_title']." = ".$tab_score["eleves"][$loop]['chaine']."\">";
		echo $tab_score["eleves"][$loop]['chaine'];
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>
<script type='text/javascript'>
	if(document.getElementById('div_lien_csv')) {
		document.getElementById('div_lien_csv').style.display='';
	}
</script>\n";
}

echo "
</div>";

require_once("../lib/footer.inc.php");
?>
