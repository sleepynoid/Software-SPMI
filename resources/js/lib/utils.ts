import type { InertiaLinkProps } from '@inertiajs/vue3';
import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>): string {
    if (typeof href === 'string') return href;
    if (typeof href === 'object' && 'url' in href) return href.url;
    return String(href);
}
