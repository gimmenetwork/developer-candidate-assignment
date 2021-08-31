class FilterQuery
{
    constructor(filters = []) {
        this.filters = filters;
    }

    getQuery = () => {
        let values = [];
        this.filters.map(filter => {
            values.push(filter.key + '=' + filter.value);
        });
        return values.join('&');
    }
}

export default FilterQuery;
