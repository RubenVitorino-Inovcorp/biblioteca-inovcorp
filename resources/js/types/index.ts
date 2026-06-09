export interface User {
    id: number;
    name: string;
    email: string;
    profile_photo_url?: string | null;
    is_admin?: boolean;
    role?: { id: number; name: string };
}
export interface Author {
    id: number;
    name: string;
    photo_url?: string;
    books_count?: number;
}
export interface Publisher {
    id: number;
    name: string;
    logo_url?: string;
    books_count?: number;
}
export interface Tag {
    id: number;
    name: string;
}
export interface Review {
    id: number;
    book: Book;
    user?: User;
    review_title: string;
    review_text: string;
    rating: number;
    status: string;
    status_label?: string;
    status_color?: string;
    created_at?: string;
    rejection_reason?: string;
}
export interface Book {
    id: number;
    title: string;
    image_url: string;
    isbn?: string;
    price?: number | string;
    bibliography?: string;
    is_available?: boolean;
    has_alert?: boolean;
    authors?: Author[];
    publisher?: Publisher;
    tags?: Tag[];
    reviews?: Review[];
}
export interface Loan {
    id: number;
    loan_number: string;
    book: Book;
    user?: User;
    user_id?: number;
    user_photo_snapshot?: string | null;
    start_date: string;
    estimated_return_date?: string;
    end_date: string | null;
    elapsed_days?: number;
    status: string;
    status_label: string;
    status_color: string;
}
export interface CartItem {
    id: number;
    book: Book;
    quantity: number;
}
export interface Cart {
    id: number;
    cart_number: string;
    items: CartItem[];
    total: string;
}
export interface OrderItem {
    id: number;
    book: Book;
    quantity: number;
    price: number;
}
export interface Order {
    id: number;
    order_number: string;
    delivery_address: string;
    items: OrderItem[];
    total: number;
    status: string;
    user?: User;
    total_price?: number;
    status_label?: string;
    status_color?: string;
    created_at?: string;
    updated_at?: string;
}
export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}
export interface PaginatedData<T> {
    data: T[];
    links: PaginationLink[];
    current_page?: number;
    last_page?: number;
    per_page?: number;
    total?: number;
}
