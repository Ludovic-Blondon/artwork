// 🔹 Types génériques
export type DataPaginated<T> = {
    data: T[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
};

// 🔹 Types métiers
export type Artist = {
    id: number;
    name: string;
    bio?: string;
    birthDate?: string;
    deathDate?: string;
};

export type Media = {
    id: number;
    url: string;
    name: string;
    size: number;
};

export type Artwork = {
    id: number;
    title: string;
    description?: string;
    yearCreated?: number;
    artist: Artist;
    images: Media[];
    featuredImage?: string | null;
};
