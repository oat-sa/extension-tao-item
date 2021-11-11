export default {
    addItem: '[data-context="resource"][data-action="instanciate"]',
    addSubClassUrl: 'taoItems/Items/addSubClass',

    booleanListValue: 'http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_Boolean',

    classOptions: '[action="/taoItems/Items/editItemClass"]',

    deleteConfirm: '[data-control="delete"]',
    deleteClass: '[data-context="class"][data-action="deleteItemClass"]',
    deleteClassUrl: 'taoItems/Items/deleteClass',
    deleteItem: '[data-context="instance"][data-action="deleteItem"]',

    editClass: '#item-class-schema',
    editClassLabelUrl: 'taoItems/Items/editClassLabel',
    editClassUrl: 'taoItems/Items/editItemClass',
    editItemUrl: 'taoItems/Items/editItem',
    exportItem: '[data-context="resource"][data-action="load"]',
    exportItemUrl: 'taoItems/ItemExport/index',

    importItem: '[data-context="resource"][data-action="loadClass"]',
    itemForm: 'form[action="/taoItems/Items/editItem"]',
    itemClassForm: 'form[action="/taoItems/Items/editClassLabel"]',
    importItemUrl: 'taoItems/ItemImport/index',

    moveClass: '[id="class-move-to"][data-context="class"][data-action="moveTo"]',
    moveConfirmSelector: 'button[data-control="ok"]',

    propertyEdit: 'div[class="form-group property-block regular-property property-edit-container-open"]',

    resourceGetAllUrl: 'tao/RestResource/getAll',
    resourceRelationsUrl: 'tao/ResourceRelations',
    root: '[data-uri="http://www.tao.lu/Ontologies/TAOItem.rdf#Item"]',

    treeRenderUrl: 'taoItems/Items',
};
