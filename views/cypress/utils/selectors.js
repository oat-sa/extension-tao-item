export default {
    addItem: '[data-context="resource"][data-action="instanciate"]',
    addSubClassUrl: 'taoItems/Items/addSubClass',

    booleanListValue: 'http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_Boolean',
    booleanListTrueValue: 'http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_True',
    booleanListFalseValue: 'http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_False',

    classOptions: '[action="/taoItems/Items/editItemClass"]',

    deleteConfirm: '[data-control="delete"]',
    deleteClass: '[data-context="class"][data-action="deleteItemClass"]',
    deleteClassUrl: 'taoItems/Items/deleteClass',
    deleteItem: '[data-context="instance"][data-action="deleteItem"]',

    editClass: '#item-class-schema',
    editClassLabelUrl: 'taoItems/Items/editClassLabel',
    editClassUrl: 'taoItems/Items/editItemClass',
    editItemUrl: 'taoItems/Items/editItem',
    exportItem: '#item-export',
    exportItemUrl: 'taoItems/ItemExport/index',

    importItem: '#item-import',
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
