// import { AppBridgeContext } from '@shopify/app-bridge-react';
import {useAppBridge} from '@shopify/app-bridge-react';


export default function AppBridgeProvider({ children }) {
    const appBridge = useAppBridge();

    const appBridgeInstance = appBridge || {
        // Fallback or default values for appBridge
        dispatch: () => {},
        subscribe: () => {},
        unsubscribe: () => {},
        getState: () => ({}),
    };

    return (
        <>
            <div id="app-bridge-provider-wrapper">{children}</div>
        </>
    );
}
