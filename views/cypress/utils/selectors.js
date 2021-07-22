export default {
    deleteItem: '[data-context="instance"][data-action="deleteItem"]',
    deleteClass: '[data-context="class"][data-action="deleteItemClass"]',
    moveClass: '[data-context="resource"][data-action="moveTo"]',
    moveConfirm: 'div[data-control="ok"]',
    addItem: '[data-context="resource"][data-action="instanciate"]',
    itemForm: 'form[action="/taoItems/Items/editItem"]',
    itemClassForm: 'form[action="/taoItems/Items/editClassLabel"]',
    deleteConfirm: '[data-control="delete"]',
    root: '[data-uri="http://www.tao.lu/Ontologies/TAOItem.rdf#Item"]'
};
