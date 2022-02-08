/** @jsx vNode */

import { vNode, addEvent, getMainContainer, changeMainContainer, myAppEventHandler, render } from '../../../node_modules/@ocdladefense/view/view.js';
import { CACHE, HISTORY } from '../../../node_modules/@ocdladefense/view/cache.js';

import { getOrders, getOrderById, getOrderItems } from './data.js';
import { HomeFullNode } from './components.js';



function init() {
    console.log("start")
}




domReady(init);