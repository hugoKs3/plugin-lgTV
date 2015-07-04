<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

global $listCmdlgTV;

include_file('core', 'lgTV', 'config', 'lgTV');
sendVarToJS('eqType', 'lgTV');
$eqLogics = eqLogic::byType('lgTV');
?>

<div class="row row-overflow">
    <div class="col-lg-2">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter une TV}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes TVs}}
        </legend>
        <div class="eqLogicThumbnailContainer">
                      <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
           <center>
            <i class="fa fa-plus-circle" style="font-size : 7em;color:#28a3d3;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;;color:#28a3d3"><center>Ajouter</center></span>
    </div>
         <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
                    echo "<center>";
                    echo '<img src="plugins/lgTV/doc/images/lgTV_icon.png" height="105" width="95" />';
                    echo "</center>";
                    echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                    echo '</div>';
                }
                ?>
            </div>
    </div>   
    <div class="col-lg-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <form class="form-horizontal">
            <fieldset>
                <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}<i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i></legend>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Nom de la TV}}</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de la TV}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label" >{{Objet parent}}</label>
                    <div class="col-lg-3">
                        <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                            <option value="">{{Aucun}}</option>
                            <?php
                            foreach (object::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Catégorie}}</label>
                    <div class="col-lg-8">
                        <?php
                        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                            echo '<label class="checkbox-inline">';
                            echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                            echo '</label>';
                        }
                        ?>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label" >{{Activer}}</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>
                    </div>
                    <label class="col-lg-2 control-label" >{{Visible}}</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Adresse IP}}</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="addr" placeholder="{{Adresse IP}}"/>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-lg-2 control-label">{{Clé d'appairage}}</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="key" placeholder="{{Clé d'appairage}}"/>
                    </div>
                </div>
				<div class="form-group">				
                    <label class="col-lg-2 control-label">{{Affichage}}</label>
					<div class="col-lg-8">
						<label class="checkbox-inline">
                            <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="has_basic" /> Base
                        </label>
						<label class="checkbox-inline">
                            <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="has_num" /> Numérique
                        </label>
						<label class="checkbox-inline">
                            <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="has_magneto" /> Magnéto
                        </label>
						<label class="checkbox-inline">
                            <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="has_color" /> Couleur
                        </label>
						<label class="checkbox-inline">
                            <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="has_divers" /> Divers (Déconseillé nombreuses commandes annexes)
                        </label>
					</div>
                </div>
				<div class="form-group">
                    <label class="col-sm-2 control-label">{{Couleur des commandes}}</label>
                        <div class="col-sm-3">
                            <select class="eqLogicAttr form-control" data-l1key='configuration' data-l2key='theme'>
                                    <option value='Blanc'>{{Bouton Blanc}}</option>
									<option value='Noir'>{{Bouton Noir}}</option>
							</select>
                        </div>
                </div>
				<div class="form-group">
                    <label class="col-sm-2 control-label">{{Pas du volume}}</label>
                        <div class="col-sm-3">
                            <select class="eqLogicAttr form-control" data-l1key='configuration' data-l2key='volnum'>
                                    <option value=1>{{1}}</option>
									<option value=2>{{2}}</option>
									 <option value=3>{{3}}</option>
									<option value=4>{{4}}</option>
									<option value=5>{{5}}</option>
							</select>
                        </div>
                </div>
            </fieldset> 
        </form>

        <legend>Commandes</legend>
        <div class="alert alert-info">
            {{Info : <br/>
            - Rajouter les blocs de commandes de votre choix en les choisissant au dessus<br/>
			- Pour trouver votre clé d'appairage. Renseignez l'IP. Mettez 0 dans clé.
			 Ajouter un bloc quelconque. Sauvegardez. Lancez un test. La clé
			 s'affichera sur la télé<br/>
			 -Il est déconseillé de ne pas afficher une commande particulière d'un bloc (sur le bloc divers vous pouvez).}}
        </div>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="width: 200px;">{{Nom}}</th>
                    <th style="width: 100px;">{{Type}}</th>
                    <th>{{Parametre(s)}}</th>
                    <th style="width: 100px;"></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
				    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<div class="modal fade" id="md_addPreConfigCmdlgTV">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>{{Ajouter une commande prédéfinie}}</h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none;" id="div_addPreConfigCmdlgTVError"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="in_addPreConfigCmdlgTVName">{{Fonctions}}</label>
                            <div class="col-lg-10">
                                <select class="form-control" id="sel_addPreConfigCmdlgTV">
                                    <?php
                                    foreach ($listCmdlgTV as $key => $cmdlgTV) {
                                        echo "<option value='" . $key . "'>" . $cmdlgTV['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>

                <div class="alert alert-success">
                    <?php
                    foreach ($listCmdlgTV as $key => $cmdlgTV) {
                        echo '<span class="description ' . $key . '" style="display : none;">' . $cmdlgTV['description'] . '</span>';
						echo '<span class="json_cmd ' . $key . ' hide" style="display : none;" >' . json_encode($cmdlgTV ) . '</span>';
                    }
                    ?>
                </div>
                
            </div>
			<div class="modal-footer">
			    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> {{Annuler}}</a>
                <a class="btn btn-success" id="bt_addPreConfigCmdlgTVSave"><i class="fa fa-check-circle"></i> {{Ajouter}}</a>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'lgTV', 'js', 'lgTV'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
