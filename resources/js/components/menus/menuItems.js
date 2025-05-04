const medicalItems = [
  {
    title: 'Medicines',
    icon: ListCheckIcon,
    children: [
      { title: 'Add Medicine', href: '/medicines/create', icon: PlusCircleIcon },
      { title: 'Manage Medicines', href: '/medicines', icon: ListFilter },
      { title: 'Medicine Categories', href: '/medicine-categories', icon: ListCollapse },
      { title: 'Add Stock', href: '/medicine-stocks/create', icon: PlusCircleIcon },
      { title: 'View Stock History', href: '/medicine-stocks', icon: FileTextIcon },
    ],
  },
  {
    title: 'Prescriptions',
    icon: FileTextIcon,
    children: [
      { title: 'New Prescription', href: '/prescriptions/create', icon: PlusCircleIcon },
      { title: 'Manage Prescriptions', href: '/prescriptions', icon: ListFilter },
    ],
  },
  {
    title: 'Patients',
    icon: UsersRound,
    children: [
      { title: 'Register Patient', href: '/patients/create', icon: PlusCircleIcon },
      { title: 'Manage Patients', href: '/patients', icon: ListFilter },
    ],
  },
  {
    title: 'Doctors',
    icon: LandmarkIcon,
    children: [
      { title: 'Add Doctor', href: '/doctors/create', icon: PlusCircleIcon },
      { title: 'Manage Doctors', href: '/doctors', icon: ListFilter },
    ],
  },
  {
    title: 'Appointments',
    icon: CalendarIcon,
    children: [
      { title: 'New Appointment', href: '/appointments/create', icon: PlusCircleIcon },
      { title: 'Manage Appointments', href: '/appointments', icon: ListFilter },
    ],
  },
  {
    title: 'Pharmacy Inventory',
    icon: ListCheckIcon,
    children: [
      { title: 'Add Inventory Item', href: '/pharmacy-inventory/create', icon: PlusCircleIcon },
      { title: 'Manage Inventory', href: '/pharmacy-inventory', icon: ListFilter },
    ],
  },
  {
    title: 'Vendors & Purchases',
    icon: FileIcon,
    children: [
      { title: 'Add Vendor', href: '/vendors/create', icon: PlusCircleIcon },
      { title: 'Manage Vendors', href: '/vendors', icon: ListFilter },
      { title: 'Create Purchase Order', href: '/purchases/create', icon: PlusCircleIcon },
      { title: 'Manage Purchases', href: '/purchases', icon: ListFilter },
    ],
  },
  {
    title: 'Reports',
    icon: ChartBar,
    children: [
      { title: 'View Reports', href: '/reports', icon: FileTextIcon },
    ],
  },
];

export default medicalItems;