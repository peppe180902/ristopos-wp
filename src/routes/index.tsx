/**
 * Internal dependencies
 */
import HomePage from '../pages/HomePage';
import OrderPage from '../pages/OrderPage';
import PosPage from '../pages/PosPage';

const routes = [
    {
        path: '/',
        element: HomePage,
    },
    {
        path: '/ristopos-order',
        element: OrderPage,
    },
    {
        path: '/pos',
        element: PosPage,
    },
];

export default routes;
