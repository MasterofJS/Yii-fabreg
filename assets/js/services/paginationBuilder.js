import { PROD } from '../modules/config';

export default function (str) {
    let links, nextLink;
    if (!PROD) { //todo remove for production
        return null;
    }
    links = str.split(',').map(function (e) {
        let rel, link;
        link = e.trim().match(/^<.*?>/ig);
        link = link[0].substring(1, link[0].length - 1);
        rel = e.trim().match(/rel=(.)*$/ig);
        rel = rel[0].substring(4);
        return [link, rel]
    });


    nextLink = null;
    links.some(function (e, i) {

        if (e[1] === 'next') {
            nextLink = e[0];
        }

        return e[1] === 'next';
    });
    return nextLink;
}