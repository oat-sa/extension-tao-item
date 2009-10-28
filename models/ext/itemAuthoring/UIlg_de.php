<?php
define("LGFR",

"<TAO:LABELS>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------    Messages de la fenêtre des variables    ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<!-- Titre de la première colonne de la partie variable -->
		<TAO:LABEL key=\"variableNameHeader\">Notiz</TAO:LABEL>
		<!-- Titre de la colonne contenant les valeurs de la partie variable -->
		<TAO:LABEL key=\"variableValueHeader\">Wert</TAO:LABEL>
		<!-- Titre de la colonne contenant les unités de la partie variable -->
		<TAO:LABEL key=\"variableUnitHeader\">Einheit</TAO:LABEL>
		<!-- Erreur rencontrée lors du chargement du fichier décrivant l'item -->
		<TAO:LABEL key=\"loadFileErr\">Die XML-Datei konnte nicht geladen werden.</TAO:LABEL>
		<!-- Label d'un événement lorsque le sujet essaies d'ajouter une valeur dans le tableau des variables  -->		
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------    Messages liés à l'ajout d'une valeur    ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"testValueTitle\">Test des Wertes.</TAO:LABEL>
		<TAO:LABEL key=\"testValueOK\">OK</TAO:LABEL>
		<TAO:LABEL key=\"testValueErr01\">Du hast keinen Wert im Text ausgewählt.</TAO:LABEL>
		<TAO:LABEL key=\"testValueErr02\">Der ausgewählte Wert passt nicht zur Einheit.</TAO:LABEL>
		<TAO:LABEL key=\"testValueErr03\">Du hast Werte UND Einheiten im Text ausgewählt.</TAO:LABEL>
		<TAO:LABEL key=\"testValueErr04\">Du hast  mehrere Werte gleichzeitig im Text ausgewählt.</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------    Messages liés à l'ajout d'une unité     ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"testUnitTitle\">Test der Einheit.</TAO:LABEL>
		<TAO:LABEL key=\"testUnitOK\">OK</TAO:LABEL>		
		<TAO:LABEL key=\"testUnitErr01\">Du hast keine Einheit im Text ausgewählt.</TAO:LABEL>
		<TAO:LABEL key=\"testUnitErr02\">Die ausgewählte Einheit passt nicht zum Wert.</TAO:LABEL>
		<TAO:LABEL key=\"testUnitErr03\">Du hast Werte UND Einheiten im Text ausgewählt.</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------        Test de réponse intermédiaire       ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"testSolEventLabel\">Test des Zwischenergebnisses</TAO:LABEL>		
		<TAO:LABEL key=\"testSolErr01\">Bitte wähle eine andere Einheit aus.</TAO:LABEL>
		<TAO:LABEL key=\"testSolErr02\">Falsches Resultat.</TAO:LABEL>
		<TAO:LABEL key=\"testSolOK\">Richtiges Resultat.</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------          Test de réponse finale            ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"testFinalEventLabel\">Test des Endergebnisses</TAO:LABEL>		
		<TAO:LABEL key=\"testFinalErr\">Falsches Resultat; bitte versuch es noch einmal.</TAO:LABEL>		
		<TAO:LABEL key=\"testFinalOK\">Gratuliere ! Du hast die richtige Antwort gefunden !</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ------   Ajout d'une variable sur l'axe  et opérations   -------- -->
		<!-- ----------------------------------------------------------------- -->
		
		<TAO:LABEL key=\"biggerValueError\">Der ausgewählte Wert ist zu groß für den Zahlenstrahl ; bitte verlängere ihn !</TAO:LABEL>
		<TAO:LABEL key=\"smallerValueError\">Der ausgewählte Wert ist zu klein für den Zahlenstrahl; bitte ändere ihn !</TAO:LABEL>
		<TAO:LABEL key=\"unitError\">Einheit nicht kompatibel</TAO:LABEL>
		<TAO:LABEL key=\"addValueOK\">OK</TAO:LABEL>
		<TAO:LABEL key=\"addValueEventLabel\">Die erste Zahl wird auf den Zahlenstrahl hinzugefügt.</TAO:LABEL>
		<TAO:LABEL key=\"addSecondValueEventLabel\">Die zweite Zahl wird auf den Zahlenstrahl hinzugefügt.</TAO:LABEL>
		<TAO:LABEL key=\"addOperationEventLabel\">Wähle eine Rechenart.</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------             Changement d'échelle           ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"scaleChangeErr01\">Der kleinste Wert muss größer als der größte Wert sein ; ändere den Zahlenstrahl.</TAO:LABEL>
		<TAO:LABEL key=\"scaleChangeErr02\">Die ausgewählte Spannweite enthält  keiner der Werte die auf dem Strahl vorhanden sind : bitte ändere die Spannweite.</TAO:LABEL>
		<TAO:LABEL key=\"scaleChangeOK\">OK</TAO:LABEL>
		<TAO:LABEL key=\"scaleChangeEventLabel\">Änderung des Zahlenstrahls</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------      Fenetre d'affichage de message        ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"messageWindowTitle\">Message CAMPUS !</TAO:LABEL>
		<TAO:LABEL key=\"messageWindowText\">Nachricht :</TAO:LABEL>
		<TAO:LABEL key=\"messageWindowButton\">OK !</TAO:LABEL>	
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------      Fenetre de choix d'une variable       ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"variableChoiceWindowTitle\">Wahl eines Wertes</TAO:LABEL>
		<TAO:LABEL key=\"variableChoiceWindowText\">Bitte wähle :</TAO:LABEL>
		<TAO:LABEL key=\"variableChoiceWindowButton\">Auswählen</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------     Fenetre de choix d'une solution        ------------ -->
		<!-- ----------------------------------------------------------------- -->		
		<TAO:LABEL key=\"testSolWindowTitle\">Eingabe der Lösung</TAO:LABEL>
		<TAO:LABEL key=\"solChoiceWindowValue\">Wert :</TAO:LABEL>
		<TAO:LABEL key=\"solChoiceWindowUnit\">Einheit :</TAO:LABEL>
		<TAO:LABEL key=\"solChoiceWindowButton\">OK !</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------     Fenetre de changement d'échelle        ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"scaleChoiceWindowTitle\">Wahl eines Maßstabs</TAO:LABEL>
		<TAO:LABEL key=\"scaleChoiceWindowButton\">OK !</TAO:LABEL>			
		<TAO:LABEL key=\"scaleChoiceWindowText\">Spannweite des Zahlenstrahls :</TAO:LABEL>
		<TAO:LABEL key=\"scaleChoiceWindowMin\">Min</TAO:LABEL>
		<TAO:LABEL key=\"scaleChoiceWindowMax\">Max</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------              Fenetre d'erreur              ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"errorWindowTitle\">CAMPUS Fehler !</TAO:LABEL>
		<TAO:LABEL key=\"errorWindowButton\">OK !</TAO:LABEL>			
		<TAO:LABEL key=\"errorWindowText\">Nachricht</TAO:LABEL>	
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------       Boutons  et Titre Fenêtre de calcul            ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"WindowTitle\">Zahlenstrahl</TAO:LABEL>
		<TAO:LABEL key=\"btnValider\">Bestätigen</TAO:LABEL>
		<TAO:LABEL key=\"btnScale\">Maßstab ändern</TAO:LABEL>			
		<TAO:LABEL key=\"btnReset\">Alles löschen !</TAO:LABEL>	
		<TAO:LABEL key=\"btnResetNext\">Neue Etappe</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------          Fenêtre récapitulatif             ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"operationTxt\">Rechenart</TAO:LABEL>
		<TAO:LABEL key=\"recapWindowTitle\">Liste der Rechnungen</TAO:LABEL>
		<!-- ----------------------------------------------------------------- -->
		<!-- ---------          Fenêtre Choix Final             ------------ -->
		<!-- ----------------------------------------------------------------- -->
		<TAO:LABEL key=\"finalChoiceWindowTitle\">Endergebnis</TAO:LABEL>
		<TAO:LABEL key=\"finalChoiceWindowText\">Wähle das Endergebnis !</TAO:LABEL>
		<TAO:LABEL key=\"finalChoiceWindowButton\">Auswählen !</TAO:LABEL>				
	</TAO:LABELS>"
, true);
?>