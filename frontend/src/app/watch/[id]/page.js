'use client';

import { useState, useEffect } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { ArrowLeft, Info } from 'lucide-react';
import VideoPlayer from '@/components/VideoPlayer';
import { apiClient } from '@/lib/api';

export default function WatchPage() {
  const params = useParams();
  const router = useRouter();
  const [movie, setMovie] = useState(null);
  const [loading, setLoading] = useState(true);
  const [showInfo, setShowInfo] = useState(false);

  useEffect(() => {
    const fetchMovie = async () => {
      try {
        const data = await apiClient.getMovie(params.id);
        setMovie(data);
      } catch (error) {
        console.error('Error fetching movie:', error);
      } finally {
        setLoading(false);
      }
    };

    if (params.id) {
      fetchMovie();
    }
  }, [params.id]);

  if (loading) {
    return (
      <div className="min-h-screen bg-black flex items-center justify-center">
        <div className="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-purple-500"></div>
      </div>
    );
  }

  if (!movie) {
    return (
      <div className="min-h-screen bg-black flex items-center justify-center">
        <div className="text-center">
          <h1 className="text-3xl text-white mb-4">Movie not found</h1>
          <button
            onClick={() => router.push('/')}
            className="px-6 py-3 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-colors"
          >
            Go back home
          </button>
        </div>
      </div>
    );
  }

  const streamUrl = apiClient.getStreamUrl(movie.id);

  return (
    <div className="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-purple-950">
      {/* Header */}
      <header className="sticky top-0 z-50 bg-black/50 backdrop-blur-xl border-b border-white/10">
        <div className="container mx-auto px-6 py-4">
          <div className="flex items-center justify-between">
            <button
              onClick={() => router.push('/')}
              className="flex items-center gap-2 text-white hover:text-purple-400 transition-colors"
            >
              <ArrowLeft className="w-5 h-5" />
              <span>Back to Browse</span>
            </button>

            <button
              onClick={() => setShowInfo(!showInfo)}
              className="flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg text-white transition-colors"
            >
              <Info className="w-5 h-5" />
              <span>Movie Info</span>
            </button>
          </div>
        </div>
      </header>

      {/* Main content */}
      <main className="container mx-auto px-6 py-8">
        <div className="max-w-7xl mx-auto">
          {/* Video Player */}
          <div className="mb-8">
            <VideoPlayer
              streamUrl={streamUrl}
              poster={movie.thumbnail_path}
              title={movie.title}
            />
          </div>

          {/* Movie Information */}
          <div className={`transition-all duration-300 ${showInfo ? 'opacity-100' : 'opacity-0 h-0 overflow-hidden'}`}>
            <div className="bg-white/5 backdrop-blur-lg border border-white/10 rounded-lg p-8">
              <h1 className="text-4xl font-bold text-white mb-4">{movie.title}</h1>

              <div className="flex flex-wrap items-center gap-4 mb-6">
                {movie.release_year && (
                  <span className="px-3 py-1 bg-purple-500/20 border border-purple-500/50 rounded-full text-purple-300">
                    {movie.release_year}
                  </span>
                )}

                {movie.genre && (
                  <span className="px-3 py-1 bg-blue-500/20 border border-blue-500/50 rounded-full text-blue-300">
                    {movie.genre}
                  </span>
                )}

                {movie.formatted_duration && (
                  <span className="text-zinc-400">
                    Duration: {movie.formatted_duration}
                  </span>
                )}

                {movie.rating && (
                  <div className="flex items-center gap-2">
                    <div className="flex">
                      {[...Array(5)].map((_, i) => (
                        <svg
                          key={i}
                          className={`w-5 h-5 ${
                            i < Math.floor(movie.rating / 2) ? 'text-yellow-400' : 'text-zinc-700'
                          }`}
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                      ))}
                    </div>
                    <span className="text-zinc-400">{movie.rating}/10</span>
                  </div>
                )}
              </div>

              {movie.description && (
                <div className="mb-6">
                  <h2 className="text-xl font-semibold text-white mb-2">Description</h2>
                  <p className="text-zinc-300 leading-relaxed">{movie.description}</p>
                </div>
              )}

              <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                {movie.resolution && (
                  <div>
                    <span className="text-zinc-500">Resolution</span>
                    <p className="text-white font-medium">{movie.resolution}</p>
                  </div>
                )}

                {movie.video_codec && (
                  <div>
                    <span className="text-zinc-500">Codec</span>
                    <p className="text-white font-medium">{movie.video_codec}</p>
                  </div>
                )}

                {movie.formatted_file_size && (
                  <div>
                    <span className="text-zinc-500">File Size</span>
                    <p className="text-white font-medium">{movie.formatted_file_size}</p>
                  </div>
                )}

                <div>
                  <span className="text-zinc-500">Added</span>
                  <p className="text-white font-medium">
                    {new Date(movie.created_at).toLocaleDateString()}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
