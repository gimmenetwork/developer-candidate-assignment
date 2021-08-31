import {get} from "lodash";
import FilterQuery from "App/Helper/FilterQuery";

class FilterManager
{
    stringLength;
    filters;
    mapping;

    constructor(filters = [], mapping = []) {
        this.stringLength = 3;
        this.filters = filters;
        this.mapping = mapping;
        this.buildFilters();
    }

    buildFilters = () => {
        const filters = [];
        get(this.filters, 'items', []).map(filter => {
            Object.keys(this.mapping).map(key => {
                if (key === get(filter, 'columnField')) {
                    const value = get(filter, 'value', '');
                    if (value.length >= this.stringLength) {
                        filters.push({
                            key: this.mapping[key],
                            value: value
                        });
                    }
                }
            });
        });

        this.filters = filters;
    }

    getFilterQuery = () => {
        return new FilterQuery(this.filters);
    }
}

export default FilterManager;