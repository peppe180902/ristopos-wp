import { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

interface Product {
  id: number;
  name: string;
  price: string;
  image: string;
  categories: string[];
}

interface Table {
  id: number;
  status: 'occupied' | 'free';
  total: number;
  orders: any[]; // You might want to define a more specific type for orders
}

export function useData() {
  const [products, setProducts] = useState<Product[]>([]);
  const [tables, setTables] = useState<Table[]>([]);
  const [categories, setCategories] = useState<string[]>([]);
  const [loading, setLoading] = useState({
    products: true,
    tables: true,
    categories: true
  });
  const [errors, setErrors] = useState<{[key: string]: string}>({});

  const fetchProducts = async () => {
    try {
      const data: any = await apiFetch({ path: '/wc/v3/products' });
      setProducts(data.map((product: any) => ({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.images[0]?.src || '',
        categories: product.categories.map((cat: any) => cat.name)
      })));
    } catch (error) {
      console.error('Errore nel recupero dei prodotti:', error);
      setErrors(prev => ({ ...prev, products: 'Errore nel recupero dei prodotti' }));
    } finally {
      setLoading(prev => ({ ...prev, products: false }));
    }
  };

  const fetchTables = async () => {
    try {
      const data = await apiFetch({ path: '/wp/v2/options/ristopos_tables' });
      if (data && typeof data === 'object') {
        const tablesArray = Object.entries(data).map(([id, info]: [string, any]) => ({
          id: parseInt(id),
          status: info.status,
          total: parseFloat(info.total),
          orders: info.orders || []
        }));
        setTables(tablesArray);
      } else {
        throw new Error('Formato dati tavoli non valido');
      }
    } catch (error) {
      console.error('Errore nel recupero dei tavoli:', error);
      setErrors(prev => ({ ...prev, tables: 'Errore nel recupero dei tavoli' }));
    } finally {
      setLoading(prev => ({ ...prev, tables: false }));
    }
  };

  const fetchCategories = async () => {
    try {
      const data: any = await apiFetch({ path: '/wc/v3/products/categories' });
      setCategories(data.map((cat: any) => cat.name));
    } catch (error) {
      console.error('Errore nel recupero delle categorie:', error);
      setErrors(prev => ({ ...prev, categories: 'Errore nel recupero delle categorie' }));
    } finally {
      setLoading(prev => ({ ...prev, categories: false }));
    }
  };

  useEffect(() => {
    fetchProducts();
    fetchTables();
    fetchCategories();
  }, []);

  return { products, tables, categories, loading, errors };
}

