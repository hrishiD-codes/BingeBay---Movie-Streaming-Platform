'use client';

import Link from 'next/link';
import { Play, Clock, Calendar } from 'lucide-react';
import { getPlaceholderImage } from '@/lib/utils';

export default function MovieCard({ movie }) {
  const placeholder = getPlaceholderImage(movie.title);

  return (
    <Link href={`/watch/${movie.id}`}>
      <div className="group relative bg-zinc-900 rounded-lg overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-purple-500/20 cursor-pointer">
        {/* Thumbnail */}
        <div className="relative aspect-[2/3] overflow-hidden">
          {movie.thumbnail_path ? (
            <img
              src={movie.thumbnail_path}
              alt={movie.title}
              className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
            />
          ) : (
            <div className={`w-full h-full bg-gradient-to-br ${placeholder.gradient} flex items-center justify-center`}>
              <span className="text-9xl font-bold text-white/30">{placeholder.letter}</span>
            </div>
          )}
          
          {/* Play overlay */}
          <div className="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
            <div className="bg-white/20 backdrop-blur-sm rounded-full p-6">
              <Play className="w-12 h-12 text-white fill-white" />
            </div>
          </div>

          {/* Duration badge */}
          {movie.formatted_duration && (
            <div className="absolute top-3 right-3 bg-black/80 backdrop-blur-sm px-3 py-1 rounded-full flex items-center gap-1">
              <Clock className="w-3 h-3 text-white" />
              <span className="text-xs text-white font-medium">{movie.formatted_duration}</span>
            </div>
          )}
        </div>

        {/* Movie info */}
        <div className="p-4">
          <h3 className="text-lg font-bold text-white mb-2 line-clamp-1 group-hover:text-purple-400 transition-colors">
            {movie.title}
          </h3>
          
          <div className="flex items-center gap-3 text-sm text-zinc-400">
            {movie.release_year && (
              <div className="flex items-center gap-1">
                <Calendar className="w-3 h-3" />
                <span>{movie.release_year}</span>
              </div>
            )}
            
            {movie.genre && (
              <span className="px-2 py-0.5 bg-zinc-800 rounded text-xs">
                {movie.genre}
              </span>
            )}
          </div>

          {movie.rating && (
            <div className="mt-2 flex items-center gap-1">
              <div className="flex">
                {[...Array(5)].map((_, i) => (
                  <svg
                    key={i}
                    className={`w-4 h-4 ${
                      i < Math.floor(movie.rating / 2) ? 'text-yellow-400' : 'text-zinc-700'
                    }`}
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                ))}
              </div>
              <span className="text-xs text-zinc-400 ml-1">{movie.rating}/10</span>
            </div>
          )}

          {movie.description && (
            <p className="mt-2 text-sm text-zinc-500 line-clamp-2">
              {movie.description}
            </p>
          )}
        </div>
      </div>
    </Link>
  );
}
