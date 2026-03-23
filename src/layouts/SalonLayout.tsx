import React from 'react';
import { Outlet } from 'react-router-dom';
import SalonHeader from '../components/SalonHeader';

export default function SalonLayout() {
  return (
    <>
      <SalonHeader />
      <Outlet />
    </>
  );
}
