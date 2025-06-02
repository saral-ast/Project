import { NavMenu } from '@shopify/app-bridge-react';

export default function Sidebar() {
    return (
        <NavMenu>
            <a href="/" rel="home">Home</a>
            <a href="/settings" rel="settings">Settings</a>
        </NavMenu>
    );
}
