import {get, replace} from 'lodash';
import DatagridDefinition from 'App/datagridDefinition'

const getColumnsForDatagrid = (name) => {
    return get(DatagridDefinition, [name, 'columns'], []);
}

const getMappingForDatagrid = name => {
    return get(DatagridDefinition, [name, 'mapping'], []);
}

const getFilterMappingForDatagrid = name => {
    return get(DatagridDefinition, [name, 'filterMapping'], []);
}

const getUrlForDatagrid = (name) => {
    return get(DatagridDefinition, [name, 'url'], null);
}

const getItemUrlForDatagrid = (name) => {
    return get(DatagridDefinition, [name, 'itemUrl'], null);
}

const getDataForDatagrid = (name, data) => {
    const mappingTable = getMappingForDatagrid(name);
    const items = [];
    (data || []).map(item => {
        let row = {};
        Object.keys(mappingTable).map(relation => {
            const key = get(mappingTable, relation, false);
            if (key) {
                row[relation] = get(item, key, '');
            }
        });
        items.push(row);
    });

    return items;
}

const getFormForDatagrid = name => {
    return get(DatagridDefinition, [name, 'formName'], null);
}

/**
 * Get the custom actions defined in datagrid definition
 *
 * @param name
 * @return {any}
 */
const getCustomActions = name => {
    return get(DatagridDefinition, [name, 'actions'], []);
}

/**
 * Get the custom action form component by name
 *
 * @param name
 * @param actionName
 * @return {null}
 */
const getCustomActionFormByActionName = (name, actionName) => {
    const actions = getCustomActions(name);
    let form = null;
    actions.map(action => {
        if (get(action, 'name') === actionName) {
            form = get(action, 'formName');
        }
    });
    return form;
}

const route = (name, params = {}) => {
    Object.keys(params).map(param => {
        name = name.replace(param, params[param]);
    });

    return name;
}

function getFormData(object) {
    const formData = new FormData();
    Object.keys(object).forEach(key => formData.append(key, object[key]));
    return formData;
}

/**
 * Format errors to be able to be used by formik errors handling processs
 * @param errors
 * @return {{}}
 */
const getFormattedErrors = errors => {
    let formattedErrors = {};
    (errors || []).map(error => {
        const message = get(error, ['cause', 'message']);
        const property = replace(get(error, ['cause', 'propertyPath'], ''), 'data\.', '');

        formattedErrors = Object.assign(formattedErrors, {
            [property]: message,
        });
    });

    return formattedErrors;
}

/**
 * Get the touched fields according to the error object
 * The errors and touched fields objects are in the format Formik need it
 *
 * @param errors
 * @return {{}}
 */
const getTouchedFieldsByErrors = errors => {
    let touchedFields = {};
    Object.keys(errors).map(key => {
        touchedFields = Object.assign(touchedFields, {
            [key]: true,
        })
    });

    return touchedFields;
}

export {
    getColumnsForDatagrid,
    getMappingForDatagrid,
    getFilterMappingForDatagrid,
    getUrlForDatagrid,
    getDataForDatagrid,
    route,
    getItemUrlForDatagrid,
    getFormForDatagrid,
    getFormData,
    getCustomActions,
    getCustomActionFormByActionName,
    getFormattedErrors,
    getTouchedFieldsByErrors
};