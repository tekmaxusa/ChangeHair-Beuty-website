import React from 'react';
import { useNavigate } from 'react-router-dom';
import { SalonMenuView } from './HomePage';

export default function MenuPage() {
  const navigate = useNavigate();
  return (
    <SalonMenuView
      onBack={() => navigate('/')}
      onOpenBooking={(categoryId) => navigate('/booking', { state: { preselectCategory: categoryId } })}
    />
  );
}
