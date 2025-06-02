import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import Sidebar from '@/components/sidebar';
// import { Layout } from 'lucide-react';

export default function AppLayout({ children }) {
    <div className="flex">
        <Sidebar />
        <main className="flex-1 overflow-hidden">
            {children}
        </main>
    </div>
}
