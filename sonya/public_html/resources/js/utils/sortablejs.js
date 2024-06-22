import axios from "axios";
import Sortable from "sortablejs";

export const createSortable = (element, url) => {
    return Sortable.create(element, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        filter: ".non-sortable",
        onEnd: async function (e) {
            let order = [],
                items = element.querySelectorAll('[data-sort]')
            Array.from(items).forEach((element, index) => {
                element.dataset.sort = index + 1;
                order.push({
                    'id': element.dataset.id,
                    'sort': element.dataset.sort,
                })
            })

            let response = await axios.post(url, order)
        }
    });
}