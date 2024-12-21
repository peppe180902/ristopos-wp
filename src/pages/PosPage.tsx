import React from 'react';
import { useData } from '../hooks/useData';

const DataTestComponent: React.FC = () => {
  const { products, tables, categories, loading, errors } = useData();

  return (
    <div className="p-4">
      <h1 className="text-2xl font-bold mb-4">Test Recupero Dati RistoPOS</h1>
      
      <div className="mb-8">
        <h2 className="text-xl font-semibold mb-2">Prodotti</h2>
        {loading.products ? (
          <p>Caricamento prodotti...</p>
        ) : errors.products ? (
          <p className="text-red-500">{errors.products}</p>
        ) : (
          <pre className="bg-gray-100 p-2 rounded">
            {JSON.stringify(products, null, 2)}
          </pre>
        )}
      </div>

      <div className="mb-8">
        <h2 className="text-xl font-semibold mb-2">Tavoli</h2>
        {loading.tables ? (
          <p>Caricamento tavoli...</p>
        ) : errors.tables ? (
          <p className="text-red-500">{errors.tables}</p>
        ) : (
          <pre className="bg-gray-100 p-2 rounded">
            {JSON.stringify(tables, null, 2)}
          </pre>
        )}
      </div>

      <div className="mb-8">
        <h2 className="text-xl font-semibold mb-2">Categorie</h2>
        {loading.categories ? (
          <p>Caricamento categorie...</p>
        ) : errors.categories ? (
          <p className="text-red-500">{errors.categories}</p>
        ) : (
          <pre className="bg-gray-100 p-2 rounded">
            {JSON.stringify(categories, null, 2)}
          </pre>
        )}
      </div>
    </div>
  );
};

export default DataTestComponent;

