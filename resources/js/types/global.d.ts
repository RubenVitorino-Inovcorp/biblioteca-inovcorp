import { route as routeFn } from 'ziggy-js';
import { User } from './index';

declare global {
    const route: typeof routeFn;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof routeFn;
    }
}

declare module '@inertiajs/core' {
    interface PageProps {
        auth: {
            user?: User;
        };
        roles?: {
            ADMIN: number;
            [key: string]: number;
        };
        jetstream?: {
            managesProfilePhotos: boolean;
            hasApiFeatures: boolean;
            hasTermsAndPrivacyPolicyFeature: boolean;
        };
        [key: string]: any;
    }
}