import { PlaceItem, Story, UserProfile } from '../types';

import villaImg from '../assets/images/travel_sanctuary_villa_1791401095106.jpg';
import coffeeImg from '../assets/images/lifestyle_artisan_coffee_1791401104130.jpg';
import creatorImg from '../assets/images/creator_portrait_female_1791401113131.jpg';
import museumImg from '../assets/images/modern_architecture_museum_1791401122446.jpg';

export const INITIAL_STORIES: Story[] = [
  {
    id: 's-1',
    author: 'Elena V.',
    avatar: creatorImg,
    title: 'Aman Sanctuary',
    image: villaImg,
    isViewed: false,
  },
  {
    id: 's-2',
    author: 'Kaito Studio',
    avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
    title: 'Minimal Museo',
    image: museumImg,
    isViewed: false,
  },
  {
    id: 's-3',
    author: 'Café Nomad',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
    title: 'Roastery Tokyo',
    image: coffeeImg,
    isViewed: true,
  },
  {
    id: 's-4',
    author: 'Sora Retiro',
    avatar: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=400&q=80',
    title: 'Pabellón Zen',
    image: villaImg,
    isViewed: true,
  },
];

export const INITIAL_PLACES: PlaceItem[] = [
  {
    id: 'p-1',
    title: 'Santuario del Bosque de Teak',
    subtitle: 'Villa eco-arquitectónica sobre el valle tropical',
    location: 'Ubud, Bali · Indonesia',
    pricePerNight: 280,
    rating: 4.98,
    reviewCount: 342,
    category: 'villas',
    image: villaImg,
    secondaryImages: [villaImg, museumImg, coffeeImg],
    description: 'Concebida como una simbiosis entre la arquitectura contemporánea y la densa vegetación tropical balinesa, esta villa privada ofrece vistas panorámicas al dosel de la jungla y una piscina infinita de basalto volcánico que refleja la niebla matutina.',
    hostName: 'Elena Valdés',
    hostAvatar: creatorImg,
    hostRole: 'Arquitecta & Superanfitriona',
    isBookmarked: true,
    amenities: [
      'Piscina infinita privada',
      'Desayuno orgánico de autor',
      'Estudio de meditación zen',
      'Internet de alta velocidad 500Mbps',
      'Servicio de conserjería 24/7'
    ],
    highlights: [
      { label: 'Espacio', value: '320 m² de diseño' },
      { label: 'Capacidad', value: 'Hasta 4 huéspedes' },
      { label: 'Clasificación', value: 'Top 1% de estancias' },
    ],
  },
  {
    id: 'p-2',
    title: 'Pabellón de Concreto y Luz',
    subtitle: 'Galería y residencia escultórica minimalista',
    location: 'Naoshima · Japón',
    pricePerNight: 420,
    rating: 4.95,
    reviewCount: 188,
    category: 'architecture',
    image: museumImg,
    secondaryImages: [museumImg, villaImg, coffeeImg],
    description: 'Inspirado en la simplicidad geométrica de Tadao Ando. Cada ángulo captura la luz natural cambiante del mar interior de Seto mediante tragaluces monolíticos y patios silenciosos.',
    hostName: 'Kaito Studio',
    hostAvatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
    hostRole: 'Fundación de Arte Contemporáneo',
    isBookmarked: false,
    amenities: [
      'Acceso exclusivo a exposiciones',
      'Biblioteca de arquitectura y diseño',
      'Baño tradicional ofuro en cedro',
      'Acústica de meditación binaural'
    ],
    highlights: [
      { label: 'Superficie', value: '450 m² con jardines' },
      { label: 'Ubicación', value: 'Frente al Mar Seto' },
      { label: 'Privacidad', value: 'Residencia completa' },
    ],
  },
  {
    id: 'p-3',
    title: 'Atelier Kōhī & Panadería Silvestre',
    subtitle: 'Laboratorio de café de especialidad y masa madre',
    location: 'Nakameguro, Tokio · Japón',
    pricePerNight: 95,
    rating: 4.92,
    reviewCount: 512,
    category: 'cafes',
    image: coffeeImg,
    secondaryImages: [coffeeImg, villaImg, museumImg],
    description: 'Un templo dedicado a la extracción de cafés Geisha de micro-lotes tostados en leña de cerezo japonés. Disfruta de repostería nórdica artesanal en una barra de mármol carrara pulida a mano.',
    hostName: 'Chef Kenji Sato',
    hostAvatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
    hostRole: 'Maestro Barista Q-Grader',
    isBookmarked: true,
    amenities: [
      'Cata privada con el maestro barista',
      'Horneado diario en vivo',
      'Espacio acústico para lectura',
      'Venta de granos edición limitada'
    ],
    highlights: [
      { label: 'Variedades', value: '8 orígenes únicos' },
      { label: 'Ambiente', value: 'Quiet space & música jazz' },
      { label: 'Experiencia', value: 'Cata sensorial guiada' },
    ],
  },
];

export const INITIAL_USER_PROFILE: UserProfile = {
  name: 'Elena Valdés',
  handle: '@elenavaldes.arch',
  role: 'Curadora Visual & Arquitecta',
  bio: 'Explorando la intersección entre el espacio habitable, la luz natural y el bienestar sereno en Kioto, Bali y Milán.',
  avatar: creatorImg,
  coverImage: villaImg,
  followers: '48.2k',
  savedPlaces: 18,
  curations: 12,
  verified: true,
};

export const PRESET_IMAGE_OPTIONS = [
  {
    id: 'villa',
    title: 'Villa Tropical de Teak',
    category: 'Arquitectura & Viajes',
    url: villaImg,
    badge: 'Generada HD',
  },
  {
    id: 'museum',
    title: 'Museo de Concreto Escultural',
    category: 'Arquitectura Minimalista',
    url: museumImg,
    badge: 'Generada HD',
  },
  {
    id: 'coffee',
    title: 'Barra de Café de Especialidad',
    category: 'Estilo de Vida & Gastronomía',
    url: coffeeImg,
    badge: 'Generada HD',
  },
  {
    id: 'creator',
    title: 'Retrato de la Diseñadora',
    category: 'Perfil & Creador',
    url: creatorImg,
    badge: 'Generada HD',
  },
  {
    id: 'kyoto_garden',
    title: 'Jardín Zen de Piedra y Musgo',
    category: 'Japón & Paisajismo',
    url: 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=1200&q=80',
    badge: 'Editorial Web',
  },
  {
    id: 'nordic_loft',
    title: 'Loft Nórdico de Techo Alto',
    category: 'Interiorismo',
    url: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
    badge: 'Editorial Web',
  },
];
