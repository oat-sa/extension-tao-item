export default {
    deleteItem: '[data-context="instance"][data-action="deleteItem"]',
    deleteClass: '[data-context="class"][data-action="deleteItemClass"]',
    newClass: '[data-context="resource"][data-action="subClass"]',
    addItem: '[data-context="resource"][data-action="instanciate"]',
    itemForm: 'form[action="/taoItems/Items/editItem"]',
    itemClassForm: 'form[action="/taoItems/Items/editClassLabel"]',
    classOptions: '[action="/taoItems/Items/editItemClass"]',
    editClass: 'ul[class="plain action-bar content-action-bar horizontal-action-bar"]',
    classForm: 'form[data-action= "/taoItems/Items/editItemClass"]',
    propertyEdit: 'div[class="form-group property-block regular-property property-edit-container-open"]',
    deleteConfirm: '[data-control="delete"]',
    selectTrue: 'input[type="radio"][value="http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_True"]',
    root: '[data-uri="http://www.tao.lu/Ontologies/TAOItem.rdf#Item"]'
};
