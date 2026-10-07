export interface Story {
  id: string;
  author: string;
  avatar: string;
  title: string;
  image: string;
  isViewed: boolean;
}

export interface PlaceItem {
  id: string;
  title: string;
  subtitle: string;
  location: string;
  pricePerNight: number;
  rating: number;
  reviewCount: number;
  category: 'villas' | 'cafes' | 'architecture' | 'retreats';
  image: string;
  secondaryImages: string[];
  description: string;
  hostName: string;
  hostAvatar: string;
  hostRole: string;
  isBookmarked: boolean;
  amenities: string[];
  highlights: { label: string; value: string }[];
}

export interface UserProfile {
  name: string;
  handle: string;
  role: string;
  bio: string;
  avatar: string;
  coverImage: string;
  followers: string;
  savedPlaces: number;
  curations: number;
  verified: boolean;
}

export type ScreenId = 'discover' | 'detail' | 'profile' | 'saved';

export type DeviceFrameColor = 'titanium-natural' | 'titanium-black' | 'titanium-desert' | 'silver' | 'minimal-bezel';

export type ViewMode = 'single' | 'showcase' | 'touch';
