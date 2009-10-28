<?php
define("LGFR",

"<TAO:LABELS>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------    Messages de la fenêtre des variables    ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<!-- Titre de la première colonne de la partie variable -->
		<TAO:LABEL key=\"variableNameHeader\">Notiz</TAO:LABEL>
		<!-- Titre de la colonne contenant les valeurs de la partie variable -->
		<TAO:LABEL key=\"variableValueHeader\">Valeur</TAO:LABEL>
		<!-- Titre de la colonne contenant les unités de la partie variable -->
		<TAO:LABEL key=\"variableUnitHeader\">unité</TAO:LABEL>
		<!-- Erreur rencontrée lors du chargement du fichier décrivant l'item -->
		<TAO:LABEL key=\"loadFileErr\">Le fichier XML n'a pas pu être chargé</TAO:LABEL>
		<!-- Label d'un événement lorsque le sujet essaies d'ajouter une valeur dans le tableau des variables  -->		
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------    Messages liés à l'ajout d'une valeur    ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"testValueTitle\">Test d'une valeur</TAO:LABEL>
		<TAO:LABEL key=\"testValueOK\">OK</TAO:LABEL>
		<TAO:LABEL key=\"testValueErr01\">Vous n'avez pas sélectionné de valeur dans l'énoncé</TAO:LABEL>
		<TAO:LABEL key=\"testValueErr02\">La valeur sélectionnée ne correspond pas à l'unité</TAO:LABEL>
		<TAO:LABEL key=\"testValueErr03\">Vous avez sélectionné des valeur ET des unités dans l'énoncé</TAO:LABEL>
		<TAO:LABEL key=\"testValueErr04\">Vous avez sélectionné plusieurs valeurs à la fois dans l'énoncé</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------    Messages liés à l'ajout d'une unité     ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"testUnitTitle\">Test d'unité</TAO:LABEL>
		<TAO:LABEL key=\"testUnitOK\">OK</TAO:LABEL>		
		<TAO:LABEL key=\"testUnitErr01\">Vous n'avez pas sélectionné d'unité dans l'énoncé</TAO:LABEL>
		<TAO:LABEL key=\"testUnitErr02\">L'unité sélectionnée ne correspond pas à la valeur</TAO:LABEL>
		<TAO:LABEL key=\"testUnitErr03\">Vous avez sélectionné des valeur ET des unités dans l'énoncé</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------        Test de réponse intermédiaire       ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"testSolEventLabel\">Test de réponse intermédiaire</TAO:LABEL>		
		<TAO:LABEL key=\"testSolErr01\">Veuillez choisir une autre unité</TAO:LABEL>
		<TAO:LABEL key=\"testSolErr02\">Réponse numérique incorrecte</TAO:LABEL>
		<TAO:LABEL key=\"testSolOK\">Réponse correcte</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------          Test de réponse finale            ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"testFinalEventLabel\">Test de réponse finale</TAO:LABEL>		
		<TAO:LABEL key=\"testFinalErr\">Réponse incorrecte, veuillez réessayer</TAO:LABEL>		
		<TAO:LABEL key=\"testFinalOK\">Félicitations, vous avez trouvé la bonne réponse</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ------   Ajout d'une variable sur l'axe  et opérations   -------- -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"biggerValueError\">La valeur choisie est trop grande pour l'échelle : Veuillez changer l'échelle</TAO:LABEL>
		<TAO:LABEL key=\"smallerValueError\">La valeur choisie est trop petite pour l'échelle : Veuillez changer l'échelle</TAO:LABEL>
		<TAO:LABEL key=\"unitError\">Unité non compatible</TAO:LABEL>
		<TAO:LABEL key=\"addValueOK\">OK</TAO:LABEL>
		<TAO:LABEL key=\"addValueEventLabel\">Ajout d'une première variable sur l'axe</TAO:LABEL>
		<TAO:LABEL key=\"addSecondValueEventLabel\">Ajout d'une variable sur l'axe</TAO:LABEL>
		<TAO:LABEL key=\"addOperationEventLabel\">Ajout d'une opération sur l'axe</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------             Changement d'échelle           ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"scaleChangeErr01\">La valeur minimale doit être supérieur à la valeur maximale : veuillez choisir une autre échelle</TAO:LABEL>
		<TAO:LABEL key=\"scaleChangeErr02\">L'échelle choisie ne comprend pas au moins une des valeurs présentent sur l'axe : veuillez choisir une autre échelle</TAO:LABEL>
		<TAO:LABEL key=\"scaleChangeOK\">OK</TAO:LABEL>
		<TAO:LABEL key=\"scaleChangeEventLabel\">Changement d'échelle</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------      Fenetre d'affichage de message        ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"messageWindowTitle\">Message CAMPUS !</TAO:LABEL>
		<TAO:LABEL key=\"messageWindowText\">Message :</TAO:LABEL>
		<TAO:LABEL key=\"messageWindowButton\">OK !</TAO:LABEL>	
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------      Fenetre de choix d'une variable       ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"variableChoiceWindowTitle\">Choix d'une variable</TAO:LABEL>
		<TAO:LABEL key=\"variableChoiceWindowText\">Veuillez choisir une variable</TAO:LABEL>
		<TAO:LABEL key=\"variableChoiceWindowButton\">Choisir</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------     Fenetre de choix d'une solution        ------------ -->
		<!-- ----------------------------------------------------------------- -->		
		<TAO:LABEL key=\"testSolWindowTitle\">Choix d'une solution</TAO:LABEL>
		<TAO:LABEL key=\"solChoiceWindowValue\">Valeur :</TAO:LABEL>
		<TAO:LABEL key=\"solChoiceWindowUnit\">Unité :</TAO:LABEL>
		<TAO:LABEL key=\"solChoiceWindowButton\">OK !</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------     Fenetre de changement d'échelle        ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"scaleChoiceWindowTitle\">Choix d'une échelle</TAO:LABEL>
		<TAO:LABEL key=\"scaleChoiceWindowButton\">OK !</TAO:LABEL>			
		<TAO:LABEL key=\"scaleChoiceWindowText\">Choisissez une échelle</TAO:LABEL>
		<TAO:LABEL key=\"scaleChoiceWindowMin\">Min</TAO:LABEL>
		<TAO:LABEL key=\"scaleChoiceWindowMax\">Max</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------              Fenetre d'erreur              ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"errorWindowTitle\">Erreur CAMPUS !</TAO:LABEL>
		<TAO:LABEL key=\"errorWindowButton\">OK !</TAO:LABEL>			
		<TAO:LABEL key=\"errorWindowText\">Message</TAO:LABEL>	
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------       Boutons Fenêtre de calcul            ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"WindowTitle\">Ligne des nombres</TAO:LABEL>
		<TAO:LABEL key=\"btnValider\">Valider</TAO:LABEL>
		<TAO:LABEL key=\"btnScale\">Changer l'échelle !</TAO:LABEL>			
		<TAO:LABEL key=\"btnReset\">Effacer tout !</TAO:LABEL>	
		<TAO:LABEL key=\"btnResetNext\">Nouvelle étape</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------          Fenêtre récapitulatif             ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"operationTxt\">Opération</TAO:LABEL>
		<TAO:LABEL key=\"recapWindowTitle\">Liste des calculs</TAO:LABEL>				
	</TAO:LABELS>"
, true);
?>