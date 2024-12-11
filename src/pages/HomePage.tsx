import React from 'react';
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

const HomePage: React.FC = () => {
    return (
        <div className="ristopos-header bg-gradient-to-r min-h-screen flex items-center justify-center">
            <div className="wrap ristopos-welcome-page p-8 bg-white rounded-lg shadow-lg transform transition duration-500 hover:scale-105">
                <h1 className="text-3xl font-bold mb-6 text-center">{__('Welcome to RistoPOS - Complete Management for Your Restaurant', 'ristopos')}</h1>

                <div className="ristopos-intro my-4 text-center">
                    <p className="text-lg">{__('RistoPOS is the integrated solution for efficient management of your restaurant. From order management to advanced reporting, RistoPOS offers all the tools you need to optimize your operations.', 'ristopos')}</p>
                </div>

                <div className="ristopos-features my-8">
                    <h2 className="text-2xl font-semibold text-center mb-4">{__('Main Features', 'ristopos')}</h2>
                    <div className="ristopos-feature-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div className="ristopos-feature-item p-6 border rounded-lg shadow-lg bg-gray-50 hover:bg-gray-100 transition duration-300">
                            <h3 className="text-xl font-semibold mb-2 flex items-center"><span className="dashicons dashicons-cart mr-2"></span> {__('Order Management', 'ristopos')}</h3>
                            <p>{__('Easily manage ongoing orders and access the complete history. Optimize the workflow from the kitchen to the dining room.', 'ristopos')}</p>
                            <Button href="/wp-admin/admin.php?page=ristopos-orders" variant="primary" className="mt-4 inline-block">{__('Manage Orders', 'ristopos')}</Button>
                        </div>
                        <div className="ristopos-feature-item p-6 border rounded-lg shadow-lg bg-gray-50 hover:bg-gray-100 transition duration-300">
                            <h3 className="text-xl font-semibold mb-2 flex items-center"><span className="dashicons dashicons-grid-view mr-2"></span> {__('Table Management', 'ristopos')}</h3>
                            <p>{__('Organize and assign tables efficiently. Monitor the status of each table in real-time.', 'ristopos')}</p>
                            <Button href="/wp-admin/admin.php?page=ristopos-tables" variant="primary" className="mt-4 inline-block">{__('Manage Tables', 'ristopos')}</Button>
                        </div>
                        <div className="ristopos-feature-item p-6 border rounded-lg shadow-lg bg-gray-50 hover:bg-gray-100 transition duration-300">
                            <h3 className="text-xl font-semibold mb-2 flex items-center"><span className="dashicons dashicons-products mr-2"></span> {__('Product Management', 'ristopos')}</h3>
                            <p>{__('Add, edit, and delete products with ease. Manage your menu dynamically.', 'ristopos')}</p>
                            <Button href="/wp-admin/admin.php?page=ristopos-product-management" variant="primary" className="mt-4 inline-block">{__('Manage Products', 'ristopos')}</Button>
                        </div>
                        <div className="ristopos-feature-item p-6 border rounded-lg shadow-lg bg-gray-50 hover:bg-gray-100 transition duration-300">
                            <h3 className="text-xl font-semibold mb-2 flex items-center"><span className="dashicons dashicons-money-alt mr-2"></span> {__('POS System', 'ristopos')}</h3>
                            <p>{__('Manage table transactions with our intuitive and fast POS system.', 'ristopos')}</p>
                            <Button href="/wp-admin/admin.php?page=ristopos-products" variant="primary" className="mt-4 inline-block">{__('Open POS', 'ristopos')}</Button>
                        </div>
                        <div className="ristopos-feature-item p-6 border rounded-lg shadow-lg bg-gray-50 hover:bg-gray-100 transition duration-300">
                            <h3 className="text-xl font-semibold mb-2 flex items-center"><span className="dashicons dashicons-chart-bar mr-2"></span> {__('Advanced Reporting', 'ristopos')}</h3>
                            <p>{__('Analyze your restaurant\'s performance with detailed reports and valuable insights.', 'ristopos')}</p>
                            <Button href="/wp-admin/admin.php?page=ristopos-analytics" variant="primary" className="mt-4 inline-block">{__('View Reports', 'ristopos')}</Button>
                        </div>
                        <div className="ristopos-feature-item p-6 border rounded-lg shadow-lg bg-gray-50 hover:bg-gray-100 transition duration-300">
                            <h3 className="text-xl font-semibold mb-2 flex items-center"><span className="dashicons dashicons-groups mr-2"></span> {__('Staff Management', 'ristopos')}</h3>
                            <p>{__('Efficiently manage your staff, assign roles, and monitor performance.', 'ristopos')}</p>
                            <Button href="/wp-admin/admin.php?page=ristopos-staff" variant="primary" className="mt-4 inline-block">{__('Manage Staff', 'ristopos')}</Button>
                        </div>
                    </div>
                </div>

                <div className="ristopos-quickstart my-8">
                    <h2 className="text-2xl font-semibold text-center mb-4">{__('Quick Start Guide', 'ristopos')}</h2>
                    <ol className="list-decimal list-inside text-lg">
                        <li><strong>{__('Configure Products:', 'ristopos')}</strong> {__('Start by adding your products in the Product Management section.', 'ristopos')}</li>
                        <li><strong>{__('Set Up Tables:', 'ristopos')}</strong> {__('Configure the layout of your restaurant\'s tables.', 'ristopos')}</li>
                        <li><strong>{__('Manage Staff:', 'ristopos')}</strong> {__('Add your staff and assign appropriate roles.', 'ristopos')}</li>
                        <li><strong>{__('Start Taking Orders:', 'ristopos')}</strong> {__('Use the POS system to start taking orders.', 'ristopos')}</li>
                        <li><strong>{__('Monitor Performance:', 'ristopos')}</strong> {__('Use the Reporting section to analyze your restaurant\'s performance.', 'ristopos')}</li>
                    </ol>
                </div>

                <div className="ristopos-support my-8 text-center">
                    <h2 className="text-2xl font-semibold mb-4">{__('Need Help?', 'ristopos')}</h2>
                    <p className="text-lg">{__('If you have any questions or need assistance, do not hesitate to contact our support team.', 'ristopos')}</p>
                    <Button href="#" isSecondary className="mt-4 inline-block">{__('Contact Support', 'ristopos')}</Button>
                </div>
            </div>
        </div>
    );
};

export default HomePage;